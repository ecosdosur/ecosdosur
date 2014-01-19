<?php

/**************************************************\
 **** APLICACI�N DE MANTENIMIENTO DE INTRANETS **** 
 **************************************************
 ******** Fichero de inclusi�n de m�dulos *********
\**************************************************/

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

/* Funciones de autocarga para las clases: llamada manual */
function autoCarga ($class_name) {

	global $_ap;

    $cargaOK = include_once("../lib/{$class_name}.class.php");
	if ( !$cargaOK )  $cargaOK = include_once("../{$_ap}/lib/{$class_name}.class.php");

	return $cargaOK;

}

/* Funciones de autocarga para las clases: llamada autom�tica */
function __autoload($class_name) {
	/*
	global $_ap;
    if ( !include_once("../lib/{$class_name}.class.php") )  require_once("../$_ap/lib/{$class_name}.class.php");
	*/
	autoCarga($class_name);
}

// Cookie de entorno de aplicaci�n
if ( isset($_GET['ap']) ) {
	$_ap = $_GET['ap'];
	setcookie('ap', $_ap);
}
else {
	$_ap = $_COOKIE['ap'];
}
if ( $_ap == '' )  die('<h1>�Acceso prohibido!</h1>');

// Fichero de configuraci�n propio de cada aplicaci�n
include_once "../$_ap/config.inc.php";

// Log de errores
include_once 'log_errors.inc.php';

?>