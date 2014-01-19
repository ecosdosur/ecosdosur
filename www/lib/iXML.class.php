<?php

/*
 * Interfaz iXML
 *
 *  Representa las operaciones que se pueden realizar relacionadas con un documento XML
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 22/11/2006
 */
interface iXML {

	/*
	 *  m�todo getXML.
	 *
	 *  Obtiene la representaci�n de un objeto en un documento XML (XMLSimple)
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: objeto XMLSimple
	 *  Lanza excepci�n si: - error en la conversi�n
	 */
	public function getXML ();

}

?>
