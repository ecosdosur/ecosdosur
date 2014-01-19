<?php

/*
 * Interfaz iInforme
 *
 *  Representa las operaciones que se pueden realizar con un formato de informe
 *
 *  Autor: Pentared
 *  Última actualización: 26/12/2006
 */
interface iInforme {

	/*
	 *  método setTitulo.
	 *
	 *  Asigna un título al informe
	 *  
	 *  Parámetros: $titulo: título que se quiere asignar
	 */
	public function setTitulo ($titulo);

	/*
	 *  método setDescripcion.
	 *
	 *  Asigna una descripción al informe
	 *  
	 *  Parámetros: $descripción: descripción del informe
	 */
	public function setDescripcion ($descripcion);

	/*
	 *  método vuelca.
	 *
	 *  Realiza el volcado de la información del informe al flujo de salida
	 *  
	 *  Parámetros: no tiene
	 */
	public function vuelca ();

}

?>
