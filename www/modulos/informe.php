<?php

/**************************************************\
 **** APLICACIÓN DE MANTENIMIENTO DE INTRANETS **** 
 **************************************************
 ********* Módulo de volcado de informes **********
\**************************************************/

// Fichero de inclusión
require_once 'includes.inc.php';

// Ejecución de la petición
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