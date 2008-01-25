<?php

/*
 * Interfaz iFactoria
 *
 *  Representa las operaciones que puede realizar una clase que
 *   actúe de acuerdo con el patrón factory.
 *
 *  Autor: Pentared
 *  Última actualización: 09/01/2007
 */
interface iFactoria {

	/*
	 *  método getInstancia.
	 *
	 *  Crea una instancia de la clase que implemente este método
	 *  
	 *  Parámetros: $parametros: parámetro o parámetros necesarios
	 *  Devuelve: un objeto de la clase que implementa este método
	 */
	public static function getInstancia ($parametros);

}

?>
