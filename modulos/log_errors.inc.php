<?php

/**************************************************\
 **** APLICACI�N DE MANTENIMIENTO DE INTRANETS **** 
 **************************************************
 ********* M�dulo de control de errores ***********
\**************************************************/

// Funci�n que manejar� los errores
function manejadorErrores ($errno, $errstr, $errfile, $errline) {

	global $_ap;

	date_default_timezone_set('Europe/Madrid');
	$fichero = '../log/'. $_ap . '-error-' . date('Ymd') . '.log';

	$ddf = fopen($fichero, 'a');
	fwrite($ddf,"[". date('H:i:s') ."] Error $errno en la l�nea $errline: $errstr\r\n");
	fclose($ddf); 

}

if ( isset($_logErroresPHP) && $_logErroresPHP ) {

	// Control de errores
	error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

	//set_error_handler('ManejadorLog::manejadorErrores');
	set_error_handler('manejadorErrores');

}

?>
