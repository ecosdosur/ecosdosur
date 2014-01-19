<?php

/*
 * Clase JSONUtils.
 *
 *  Define métodos que representan transformaciones en JSON.
 *
 *  Autor: Pentared
 *  Última actualización: 22/11/2006
 */
final class JSONUtils {

	// Por ahora no es instanciable
	private function __construct () { }

	/*
	 *  método simpleArrayToJSON.
	 *
	 *  Convierte a JSON un array unidimensional.
	 *   Si es asociativo, preservará las claves; si no lo es, asignará ordinales
	 *  
	 *  Parámetros: $arraySimple: array unidimensional
	 *  Devuelve: una cadena que representa el array en JSON
	 */
	public static function simpleArrayToJSON ($arraySimple) {

		$arrayJSON = array();
		foreach ( $arraySimple as $clave => $valor ) {
			$clave = str_replace('"', '\"', $clave);
			$valor = str_replace('"', '\"', $valor);
			$arrayJSON[] = '"' . $clave . '": "' . $valor . '"';
		}

		return '{' . join(', ', $arrayJSON) . '}';

	}

}

?>
