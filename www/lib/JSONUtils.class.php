<?php

/*
 * Clase JSONUtils.
 *
 *  Define m�todos que representan transformaciones en JSON.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 22/11/2006
 */
final class JSONUtils {

	// Por ahora no es instanciable
	private function __construct () { }

	/*
	 *  m�todo simpleArrayToJSON.
	 *
	 *  Convierte a JSON un array unidimensional.
	 *   Si es asociativo, preservar� las claves; si no lo es, asignar� ordinales
	 *  
	 *  Par�metros: $arraySimple: array unidimensional
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
