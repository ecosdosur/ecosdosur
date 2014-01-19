<?php

/*
 * Interfaz iInforme
 *
 *  Representa las operaciones que se pueden realizar con un formato de informe
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 26/12/2006
 */
interface iInforme {

	/*
	 *  m�todo setTitulo.
	 *
	 *  Asigna un t�tulo al informe
	 *  
	 *  Par�metros: $titulo: t�tulo que se quiere asignar
	 */
	public function setTitulo ($titulo);

	/*
	 *  m�todo setDescripcion.
	 *
	 *  Asigna una descripci�n al informe
	 *  
	 *  Par�metros: $descripci�n: descripci�n del informe
	 */
	public function setDescripcion ($descripcion);

	/*
	 *  m�todo vuelca.
	 *
	 *  Realiza el volcado de la informaci�n del informe al flujo de salida
	 *  
	 *  Par�metros: no tiene
	 */
	public function vuelca ();

}

?>
