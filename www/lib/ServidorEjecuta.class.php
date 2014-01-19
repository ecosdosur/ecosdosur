<?php

/*
 * Clase ServidorEjecuta.
 *
 *  Representa un servidor en el que se pueden realizar varias ejecuciones en base de datos
 *   que genera una respuesta en formato XML.
 *   Hereda de Servidor.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 23/11/2006
 */
class ServidorEjecuta extends Servidor {

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
		if ( !$this->hayVariable('numAcciones') ) {
			throw new Excepcion("falta la variable 'numAcciones'", __METHOD__);
		}
		$numAcciones = trim($this->getVariable('numAcciones'));
		if ( ($numAcciones === false ) || !is_numeric($numAcciones) ) {
			throw new Excepcion("variable 'numAcciones' no v�lida ($numAcciones)", __METHOD__);
		}

		// Ejecuci�n de todas las acciones
		$this->xmlResultado = new XMLSimple('resultado-consulta', false);
		for ( $k = 1; $k <= $numAcciones; $k++ ) {
			$xmlAccion = $this->_ejecutaAccion($k);
			$this->xmlResultado->addNodo($xmlAccion);
		}

	}

	/*
	 *  m�todo _ejecutaAccion.
	 *
	 *  Ejecuta una acci�n de las pedidas al servidor.
	 *  
	 *  Par�metros: $codAccion: c�digo de acci�n
	 *
	 *  Devuelve: un documento XML de etiqueta 'operacion' con error visible
	 *  Lanza excepci�n si: - error en las variables o en base de datos
	 */
	private function _ejecutaAccion ($codAccion) {

		/* Variables:
		  - tipoOperacion$codAccion: tipo de operaci�n ('INSERT', 'UPDATE', 'DELETE' o 'LIBRE')
		  - cnx$codAccion (opcional): conexi�n a base de datos (por defecto la conexi�n por defecto)
		  - las dem�s dependen del tipo de operaci�n
		*/

		// Variables del XML
		$error = '';
		$numFilasAfectadas = 0;

		try {
			$tipoOperacion = strtoupper($this->getVariable("tipoOperacion$codAccion"));
			$sql = '';
			$libtabla = $this->getVariable("libtabla$codAccion");
			$camposInsert = $this->getVariable("camposInsert$codAccion");
			$valoresInsert = $this->getVariable("valoresInsert$codAccion");
			$camposUpdate = $this->getVariable("camposUpdate$codAccion");
			$condicionUpdate = $this->getVariable("condicionUpdate$codAccion");
			$condicionDelete = $this->getVariable("condicionDelete$codAccion");
			switch ( $tipoOperacion ) {
				case 'INSERT': { 
					$sql = "INSERT INTO ${libtabla} (${camposInsert}) VALUES (${valoresInsert})"; 
					//echo $sql;
					break;
				}
				case 'UPDATE': { 
					 $sql = "UPDATE {$libtabla} SET {$camposUpdate} WHERE {$condicionUpdate}";
					 break;
				}
				case 'DELETE': {
					$sql = "DELETE FROM {$libtabla} WHERE {$condicionDelete}";
					break;
				}
				case 'LIBRE': { 
					$sql = $this->getVariable("sql$codAccion");
					//$sql = $this->interpreta($sql);
					break; }
				default: throw new Excepcion("tipo de operaci�n no v�lido ($tipoOperacion)", __METHOD__);
			}

			// Conexi�n a base de datos
			$cnx = $this->getVariable("cnx$codAccion");
			$conector = $this->getConector($cnx);
			$numFilasAfectadas = $conector->ejecuta($sql);			
			// No se ha producido ning�n error: volcar a log
			$this->notifica($tipoOperacion, $this->getVariable('usuario'), $libtabla);
		}
		catch (Exception $ex) {
			$error = $ex->getMessage();
		}

		// Creaci�n del XML de respuesta de esta acci�n
		$xmlAccion = new XMLSimple('operacion', true);
		$xmlAccion->addVariable('numFilasAfectadas', $numFilasAfectadas);
		$xmlAccion->addVariable('id', $codAccion);
		$xmlAccion->setError($error);

		return $xmlAccion;

	}
	
}

?>