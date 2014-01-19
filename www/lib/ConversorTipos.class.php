<?php

/*
 * Clase ConversorTipos.
 *
 *  Representa un conversor de datos genérico según el tipo de dato en cada caso.
 *   Es una clase abstracta. Implementa la interfaz iFactoria
 *
 *  Autor: Pentared
 *  Última actualización: 29/01/2007
 */
abstract class ConversorTipos implements iFactoria {

	// Constructor: no hace nada
	public function __construct () { }

	/*
	 *  método getInstancia.
	 *
	 *  Crea un objeto conversor según el tipo de conversor seleccionado.
	 *   Este método no puede ser sobrepuesto por las clases hijas
	 *  
	 *  Parámetros: $tipoConversor: nombre del conversor.
	 *  Devuelve: un objeto conversor del tipo seleccionado.
	 *  Lanza excepción si: - no se encuentra el tipo de conversor seleccionado.
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
	 *  método convierte.
	 *
	 *  Realiza la ejecutoria del conversor. 
	 *   Cada clase que herede de ella debe instanciarla para realizar su propia conversión.
	 *  
	 *  Parámetros: $dato: dato a convertir
	 *              $tipoDato: tipo de dato del valor a convertir
	 *  Devuelve: el dato convertido
	 *  Lanza excepción si: - el tipo de dato no es conocido
	 */
	abstract public function convierte ($dato, $tipoDato);
	
}

?>