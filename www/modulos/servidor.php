<?php

/**************************************************\
 **** APLICACIÓN DE MANTENIMIENTO DE INTRANETS **** 
 **************************************************
 **** Módulo servidor para llamadas asíncronas ****
\**************************************************/

// Fichero de inclusión
require_once 'includes.inc.php';

// XML de respuesta
$xmlRespuesta = null;

// Manejador de log
$objLog = new ManejadorLog($_rutaLog);

// Parámetros generales
$tipoServidor = $_POST['tipoServidor'];
$tipoRespuesta = $_POST['tipoRespuesta'];

// Ejecución de la petición
try {
	$servidor = Servidor::getInstancia($tipoServidor);
	$servidor->addVariables($_POST);
	$servidor->setLog($objLog);
	$servidor->ejecutaAcciones();
	$xmlRespuesta = $servidor->getXML();
}
catch (Exception $ex) {
	// Caso de error
	$xmlRespuesta = new XMLSimple('resultado-consulta', true);
	$xmlRespuesta->setError($ex->getMessage());
}

// Volcado de la respuesta
if ( $tipoRespuesta == 'XML' ) {
	header("Content-Type: text/xml;charset=ISO-8859-1");
	echo '<?xml version="1.0" encoding="ISO-8859-1" ?>';
	echo $xmlRespuesta->getString();
}
else {
	header("Content-Type: text/html;charset=ISO-8859-1");
	echo $xmlRespuesta->toJSON();
}

?>