<?php

/*
 * Clase ServidorEvalua.
 *
 *  Representa un servidor en el que se pueden realizar varias sentencias directas PHP
 *   que genera una respuesta en formato XML.
 *   Hereda de Servidor.
 *
 *  Autor: Pentared
 *  Última actualización: 15/12/2006
 */
class ServidorEvalua extends Servidor {

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
		$numAcciones = trim($this->getVariable('numAcciones'));
		if ( ($numAcciones === false ) || !is_numeric($numAcciones) )  $numAcciones = 1;

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
				$xmlAccion->setEtiqueta('accion');
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
		  - expresion$codAccion: consulta SQL a realizar (puede tener variables dentro)
		*/

		// XML a devolver
		$xmlAccion = null;

		try {
			// Variable esencial
			if ( !$this->hayVariable("expresion$codAccion") ) {
				throw new Excepcion("Falta la variable 'expresion$codAccion'", __METHOD__);
			}
			$expresion = $this->getVariable("expresion$codAccion");

			if ( eval("\$resultadoExpresion = $expresion;") === false ) {
				throw new Excepcion("error en la expresión ($expresion)", __METHOD__);
			}

			// Resultado devuelto
			$xmlAccion = new XMLSimple('resultado-consulta', true);
			$xmlAccion->setContenido($resultadoExpresion);
		}
		catch (Exception $ex) {
			$xmlAccion = new XMLSimple('resultado-consulta', true);
			$xmlAccion->setError($ex->getMessage());
		}

		return $xmlAccion;

	}
	
}

?>