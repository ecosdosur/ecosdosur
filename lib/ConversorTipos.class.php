<?php

/*
 * Clase ConversorTipos.
 *
 *  Representa un conversor de datos gen�rico seg�n el tipo de dato en cada caso.
 *   Es una clase abstracta. Implementa la interfaz iFactoria
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 29/01/2007
 */
abstract class ConversorTipos implements iFactoria {

	// Constructor: no hace nada
	public function __construct () { }

	/*
	 *  m�todo getInstancia.
	 *
	 *  Crea un objeto conversor seg�n el tipo de conversor seleccionado.
	 *   Este m�todo no puede ser sobrepuesto por las clases hijas
	 *  
	 *  Par�metros: $tipoConversor: nombre del conversor.
	 *  Devuelve: un objeto conversor del tipo seleccionado.
	 *  Lanza excepci�n si: - no se encuentra el tipo de conversor seleccionado.
	 */
	public static final function getInstancia ($tipoConversor) {

		$tipoServidor = trim($tipoConversor);
		$nombreClase = 'ConversorTipos' . $tipoConversor;

		if ( ($tipoConversor == '') || !autoCarga($nombreClase) ) {
			throw new Excepcion('Conversor no encontrado', __METHOD__);
		}

		return new $nombreClase();

	}

	/*
	 *  m�todo convierte.
	 *
	 *  Realiza la ejecutoria del conversor. 
	 *   Cada clase que herede de ella debe instanciarla para realizar su propia conversi�n.
	 *  
	 *  Par�metros: $dato: dato a convertir
	 *              $tipoDato: tipo de dato del valor a convertir
	 *  Devuelve: el dato convertido
	 *  Lanza excepci�n si: - el tipo de dato no es conocido
	 */
	abstract public function convierte ($dato, $tipoDato);
	
}

?>