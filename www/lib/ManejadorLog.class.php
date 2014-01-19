<?php

/*
 * Clase ManejadorLog.
 *
 *  Gestiona los avisos de actividad mediante log
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 15/12/2006
 */
class ManejadorLog {

	// Ruta del fichero de log
	protected $rutaFicheroLog;

	/*
	 *  Constructor de la clase.
	 *
	 *  Realiza las operaciones de inicializaci�n
	 *  
	 *  Par�metros: $rutaDirectorio: ruta del directorio de log
	 *              $prefijoFichero: prefijo del fichero de log
	 */
	public function __construct ($rutaDirectorio = '', $prefijoFichero = '') { 

		// Control de par�metros
		$rutaDirectorio = str_replace("\\", '/', $rutaDirectorio);
		if ( $rutaDirectorio == '' )  $rutaDirectorio = './';
		if ( substr($rutaDirectorio, strlen($rutaDirectorio) - 1, 1) != '/' ) {
			$rutaDirectorio .= '/';
		}
		//if ( $prefijoFichero == '' )  $prefijoFichero = 'log_';

		// Ruta del fichero de log (excluyendo fecha y extensi�n)
		$this->rutaFicheroLog = $rutaDirectorio . $prefijoFichero;

		// Para el correcto funcionamiento de date()
		date_default_timezone_set('Europe/Madrid');

	}

	/*
	 *  m�todo getRutaLog.
	 *
	 *  Obtiene la ruta del fichero de log de hoy.
	 *  
	 *  Par�metros: no tiene
	 */
	protected function getRutaLog () {

		return $this->rutaFicheroLog . date('Ymd');

	}

	/*
	 *  m�todo notifica.
	 *
	 *  Realiza una notificaci�n con los par�metros indicados
	 *  
	 *  Par�metros: $operacion: operaci�n que se realiza
	 *              $usuario: usuario que realiza la operaci�n
	 *              $tabla: tabla sobre la que se realiza la operaci�n
	 *  Lanza excepci�n si: - no se puede abrir el fichero de log
	 */
	public function notifica ($operacion, $usuario, $tabla = '') {

		// Nombre completo del fichero de log
		$rutaLog = $this->getRutaLog();

		// Fichero de log
		$idFichero = fopen($rutaLog, 'a');
		if ( $idFichero === false )  throw new Excepcion("no se puede abrir el fichero '$rutaLog'", __METHOD__);

		// L�nea del log
		$linea = '[' . date('H:i:s') . "] El usuario '$usuario' ha realizado un $operacion";
		if ( $tabla != '' )  $linea .= " sobre la tabla '$tabla'";
		$linea .= ".\r\n";
		fwrite($idFichero, $linea);
		fclose($idFichero);

	}

}

?>