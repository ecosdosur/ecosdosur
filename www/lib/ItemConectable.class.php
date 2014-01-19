<?php

/*
 * Clase ItemConectable.
 *
 *  Representa un objeto genérico que tiene capacidad de almacenar variables, traducir cadenas y conectarse
 *   a base de datos. Hereda de Interprete.
 *
 *  IMPORTANTE: el módulo que incluye un objeto de una clase que herede a esta (en principio, 'servidor.php') ha de tener
 *    una serie de variables globales definidas para su correcto funcionamiento:
 *    - $_db: array con las diferentes conexiones del sistema y los parámetros de cada una de ellas, y
 *    - $_cnx: nombre de la conexión por defecto.
 *
 *  Autor: Pentared
 *  Última actualización: 09/01/2007
 */
abstract class ItemConectable extends Interprete {

	// Constructor: inicializa variables
	public function __construct ($arrayVariables = false) { 

		parent::__construct($arrayVariables);

	}

	/*
	 *  método getConector.
	 *
	 *  Obtiene el conector para la conexión dada y en función de las variables globales.
	 *  
	 *  Parámetros: $cnx: nombre de la conexión a base de datos
	 *  Devuelve: un conector a base de datos
	 *  Lanza excepción si: - error en la obtención del conector
	 */
	protected function getConector ($cnx = false) {

		global $_db;
		global $_cnx;

		// Control de variables globales
		if ( !isset($_db) )  throw new Excepcion('falta la variable global $_db', __METHOD__);
		if ( !isset($_cnx) )  throw new Excepcion('falta la variable global $_cnx', __METHOD__);

		// Control de conexión existente
		if ( ($cnx === false) || ($cnx == '') )  $cnx = $_cnx;
		if ( !array_key_exists($cnx, $_db) ) {
			throw new Excepcion("No existe la conexión a base de datos '$cnx'", __METHOD__);
		}

		// Obtención del conector
		return Conector::getInstancia($_db[$cnx]);

	}
	
}

?>