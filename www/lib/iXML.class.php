<?php

/*
 * Interfaz iXML
 *
 *  Representa las operaciones que se pueden realizar relacionadas con un documento XML
 *
 *  Autor: Pentared
 *  Última actualización: 22/11/2006
 */
interface iXML {

	/*
	 *  método getXML.
	 *
	 *  Obtiene la representación de un objeto en un documento XML (XMLSimple)
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: objeto XMLSimple
	 *  Lanza excepción si: - error en la conversión
	 */
	public function getXML ();

}

?>
