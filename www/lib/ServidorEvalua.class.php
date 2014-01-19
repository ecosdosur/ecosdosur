<?php

/*
 * Clase ServidorEvalua.
 *
 *  Representa un servidor en el que se pueden realizar varias sentencias directas PHP
 *   que genera una respuesta en formato XML.
 *   Hereda de Servidor.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 15/12/2006
 */
class ServidorEvalua extends Servidor {

	// Constructor
	public function __construct () { 

		parent::__construct();

	}


	/*
	 *  m�todo _ejecutaAcciones.
	 *
	 *  Realiza la ejecutoria del servidor. Es llamado desde el m�todo 'ejecutaAcciones()' de la
	 *   clase base.
	 *  
	 *  Par�metros: no tiene
	 *  Lanza excepci�n si: - no hay variables definidas en el servidor o error en variables clave
	 */
	protected function _ejecutaAcciones () {

		// Comprobaciones sobre numAcciones
		$numAcciones = trim($this->getVariable('numAcciones'));
		if ( ($numAcciones === false ) || !is_numeric($numAcciones) )  $numAcciones = 1;

		/* Modos de funcionamiento:
		   - numAcciones = 1
		     En este caso, el xml que se devuelve representa una �nica consulta y el resultado
			 de dicha consulta se ubica directamente a partir del nodo ra�z de la respuesta.
		   - numAcciones > 1
		     En este otro caso, el resultado de cada consulta ir� en su propio nodo 'consulta' 
			 con un atributo 'id' correspondiente al n�mero de acci�n. Si hay un error, no realiza 
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
	 *  m�todo _ejecutaAccion.
	 *
	 *  Ejecuta una acci�n de las pedidas al servidor.
	 *  
	 *  Par�metros: $codAccion: c�digo de acci�n
	 *
	 *  Devuelve: un documento XML de etiqueta 'resultado-consulta' con error visible
	 *  Lanza excepci�n si: - error en las variables o en base de datos
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
				throw new Excepcion("error en la expresi�n ($expresion)", __METHOD__);
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