<?php

/**************************************************\
 **** APLICACI�N DE MANTENIMIENTO DE INTRANETS **** 
 **************************************************
 **** M�dulo servidor para llamadas as�ncronas ****
\**************************************************/

// Fichero de inclusi�n
require_once 'includes.inc.php';

// XML de respuesta
$xmlRespuesta = null;

// Manejador de log
$objLog = new ManejadorLog($_rutaLog);

// Par�metros generales
$tipoServidor = $_POST['tipoServidor'];
$tipoRespuesta = $_POST['tipoRespuesta'];

// Ejecuci�n de la petici�n
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