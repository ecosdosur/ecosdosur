<?php

/*
 * Interfaz iFactoria
 *
 *  Representa las operaciones que puede realizar una clase que
 *   act�e de acuerdo con el patr�n factory.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 09/01/2007
 */
interface iFactoria {

	/*
	 *  m�todo getInstancia.
	 *
	 *  Crea una instancia de la clase que implemente este m�todo
	 *  
	 *  Par�metros: $parametros: par�metro o par�metros necesarios
	 *  Devuelve: un objeto de la clase que implementa este m�todo
	 */
	public static function getInstancia ($parametros);

}

?>
