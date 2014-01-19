<?php

/*
 * Clase ServidorConsulta.
 *
 *  Representa un servidor en el que se pueden realizar varias consultas a base de datos
 *   que genera una respuesta en formato XML.
 *   Hereda de Servidor.
 *
 *  Autor: Pentared
 *  Última actualización: 09/01/2007
 */
class ServidorConsulta extends Servidor {

	// Constructor
	public function __construct () { 

		parent::__construct();

	}


	/*
	 *  método _ejecutaAcciones.
	 *
	 *  Realiza la ejecutoria del servidor. Es llamado desde el método 'ejecutaAcciones()' de la
	 *   clase base.
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - no hay variables definidas en el servidor o error en variables clave
	 */
	protected function _ejecutaAcciones () {

		// Comprobaciones sobre numAcciones
		if ( !$this->hayVariable('numAcciones') ) {
			throw new Excepcion("falta la variable 'numAcciones'", __METHOD__);
		}
		$numAcciones = trim($this->getVariable('numAcciones'));
		if ( ($numAcciones === false ) || !is_numeric($numAcciones) ) {
			throw new Excepcion("variable 'numAcciones' no válida ($numAcciones)", __METHOD__);
		}

		/* Modos de funcionamiento:
		   - numAcciones = 1
		     En este caso, el xml que se devuelve representa una única consulta y el resultado
			 de dicha consulta se ubica directamente a partir del nodo raíz de la respuesta.
		   - numAcciones > 1
		     En este otro caso, el resultado de cada consulta irá en su propio nodo 'consulta' 
			 con un atributo 'id' correspondiente al número de acción. Si hay un error, no realiza 
			 ninguna de las acciones.
		*/
		if ( $numAcciones == 1 ) {
			$this->xmlResultado = $this->_ejecutaAccion('');
		}
		else {
			$this->xmlResultado = new XMLSimple('resultado-consulta', true);
			for ( $k = 1; $k <= $numAcciones; $k++ ) {
				$xmlAccion = $this->_ejecutaAccion($k);
				$xmlAccion->setEtiqueta('consulta');
				$xmlAccion->addVariable('id', $k);
				$this->xmlResultado->addNodo($xmlAccion);
			}
		}

	}

	/*
	 *  método _ejecutaAccion.
	 *
	 *  Ejecuta una acción de las pedidas al servidor.
	 *  
	 *  Parámetros: $codAccion: código de acción
	 *
	 *  Devuelve: un documento XML de etiqueta 'resultado-consulta' con error visible
	 *  Lanza excepción si: - error en las variables o en base de datos
	 */
	private function _ejecutaAccion ($codAccion) {

		/* Variables:
		  - sql$codAccion: consulta SQL a realizar (puede tener variables dentro)
		  - cnx$codAccion (opcional): conexión a base de datos (por defecto la conexión por defecto)
		  - plantilla$codAccion (opcional): plantilla a pasar
		  - filaInicial$codAccion (opcional): fila inicial del resultado (0 por defecto)
		  - numFilas$codAccion (opcional): número de resultados que se pretenden (todos por defecto)
		  - libtabla$codAccion (opcional): tabla en la que se ha hecho la operación (para log)
		*/

		// XML a devolver
		$xmlAccion = null;

		try {
			// Variable esencial
			if ( !$this->hayVariable("sql$codAccion") ) {
				throw new Excepcion("Falta la variable 'sql$codAccion'", __METHOD__);
			}
			$sql = $this->getVariable("sql$codAccion");

			// Variables extra
			$cnx = $this->getVariable("cnx$codAccion");
			$plantilla = $this->getVariable("plantilla$codAccion");
			$filaInicial = $this->getVariable("filaInicial$codAccion");
			$numFilas = $this->getVariable("numFilas$codAccion");
			$libtabla = $this->getVariable("libtabla$codAccion");

			if ( ($filaInicial !== false) && ($filaInicial != '') && !is_numeric($filaInicial) ) {
				throw new Excepcion("Variable 'filaInicial$codAccion' no válida ($filaInicial)", __METHOD__);
			}
			if ( ($numFilas !== false) && ($numFilas != '') &&!is_numeric($numFilas) ) {
				throw new Excepcion("Variable 'numFilas$codAccion' no válida ($numFilas)", __METHOD__);
			}

			// Traducción de la consulta
			$sql = $this->interpreta($sql);

			// Realización de la consulta
			// - Conexión a base de datos: por ahora en variables globales
			$conector = $this->getConector($cnx);
			// - Consulta: formato español
			$lector = $conector->consulta($sql);
			$lector->setConversorTipos(ConversorTipos::getInstancia('ES'));
			// - Formateo
			$formateador = new FormateadorResultado();
			if ( $plantilla != '' )  $formateador->setPlantilla($plantilla);
			if ( $filaInicial != '' )  $formateador->setFilaInicial($filaInicial);
			if ( $numFilas != '' )  $formateador->setNumFilas($numFilas);
			$formateador->setLectorResultado($lector);

			// Resultado devuelto
			$xmlAccion = $formateador->getXML();
			$xmlAccion->setErrorVisible(true);

			// No se ha producido ningún error: volcar a log
			$this->notifica('SELECT', $this->getVariable('usuario'), $libtabla);
		}
		catch (Exception $ex) {
			$xmlAccion = new XMLSimple('resultado-consulta', true);
			$xmlAccion->setError($ex->getMessage());
		}

		return $xmlAccion;

	}
	
}

?>