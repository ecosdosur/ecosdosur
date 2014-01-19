<?php

/*
 * Clase ConversorTiposES.
 *
 *  Representa un conversor de datos al tipo usado en Espa�a.
 *   Es una clase abstracta.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 24/11/2006
 */
class ConversorTiposES extends ConversorTipos {

	// Constructor: no hace nada
	public function __construct () { 
	
		parent::__construct();

	}

	/*
	 *  m�todo convierte.
	 *
	 *  Realiza la ejecutoria del conversor. 
	 *  
	 *  Par�metros: $dato: dato a convertir
	 *              $tipoDato: tipo de dato del valor a convertir
	 *  Devuelve: el dato convertido
	 *  Lanza excepci�n si: - el tipo de dato no es conocido
	 */
	public function convierte ($dato, $tipoDato) {

		$tipoDato = strtoupper($tipoDato);

		switch ( $tipoDato ) {
			case 'BLOB': break;
			case 'DATE': $dato = implode('/', array_reverse(explode('-', $dato))); break;
			case 'COUNTER':
			case 'INTEGER':
			case 'INTEGER UNSIGNED':
			case 'INT': break; // $dato = number_format($dato, 0, ',', '.'); break;
			case 'DECIMAL':
			case 'REAL': $dato = number_format($dato, 2, ',', '.'); break;
			case 'CHAR':
			case 'TEXT':
			case 'VARCHAR':
			case 'LONGCHAR':
			case 'STRING': break;
			default: throw new Excepcion("tipo de datos no conocido ($tipoDato)", __METHOD__);
		}

		return $dato;

	}
	
}

?>