<?php

/*
 * Clase ItemConectable.
 *
 *  Representa un objeto gen�rico que tiene capacidad de almacenar variables, traducir cadenas y conectarse
 *   a base de datos. Hereda de Interprete.
 *
 *  IMPORTANTE: el m�dulo que incluye un objeto de una clase que herede a esta (en principio, 'servidor.php') ha de tener
 *    una serie de variables globales definidas para su correcto funcionamiento:
 *    - $_db: array con las diferentes conexiones del sistema y los par�metros de cada una de ellas, y
 *    - $_cnx: nombre de la conexi�n por defecto.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 09/01/2007
 */
abstract class ItemConectable extends Interprete {

	// Constructor: inicializa variables
	public function __construct ($arrayVariables = false) { 

		parent::__construct($arrayVariables);

	}

	/*
	 *  m�todo getConector.
	 *
	 *  Obtiene el conector para la conexi�n dada y en funci�n de las variables globales.
	 *  
	 *  Par�metros: $cnx: nombre de la conexi�n a base de datos
	 *  Devuelve: un conector a base de datos
	 *  Lanza excepci�n si: - error en la obtenci�n del conector
	 */
	protected function getConector ($cnx = false) {

		global $_db;
		global $_cnx;

		// Control de variables globales
		if ( !isset($_db) )  throw new Excepcion('falta la variable global $_db', __METHOD__);
		if ( !isset($_cnx) )  throw new Excepcion('falta la variable global $_cnx', __METHOD__);

		// Control de conexi�n existente
		if ( ($cnx === false) || ($cnx == '') )  $cnx = $_cnx;
		if ( !array_key_exists($cnx, $_db) ) {
			throw new Excepcion("No existe la conexi�n a base de datos '$cnx'", __METHOD__);
		}

		// Obtenci�n del conector
		return Conector::getInstancia($_db[$cnx]);

	}
	
}

?>