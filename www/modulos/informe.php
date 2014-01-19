<?php

/**************************************************\
 **** APLICACI�N DE MANTENIMIENTO DE INTRANETS **** 
 **************************************************
 ********* M�dulo de volcado de informes **********
\**************************************************/

// Fichero de inclusi�n
require_once 'includes.inc.php';

// Ejecuci�n de la petici�n
try {
	$informe = new Informe($_libreriaSistema, $_GET);
	$informe->generaInforme();
}
catch (Exception $ex) {
	// Caso de error
	//header("Content-Type: text/html;charset=ISO-8859-1");
	//echo $ex->getMessage();
	header('location: error.php?msgerror=' . $ex->getMessage());
}

?>