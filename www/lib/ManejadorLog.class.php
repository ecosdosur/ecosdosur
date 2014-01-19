<?php

/*
 * Clase ManejadorLog.
 *
 *  Gestiona los avisos de actividad mediante log
 *
 *  Autor: Pentared
 *  Última actualización: 15/12/2006
 */
class ManejadorLog {

	// Ruta del fichero de log
	protected $rutaFicheroLog;

	/*
	 *  Constructor de la clase.
	 *
	 *  Realiza las operaciones de inicialización
	 *  
	 *  Parámetros: $rutaDirectorio: ruta del directorio de log
	 *              $prefijoFichero: prefijo del fichero de log
	 */
	public function __construct ($rutaDirectorio = '', $prefijoFichero = '') { 

		// Control de parámetros
		$rutaDirectorio = str_replace("\\", '/', $rutaDirectorio);
		if ( $rutaDirectorio == '' )  $rutaDirectorio = './';
		if ( substr($rutaDirectorio, strlen($rutaDirectorio) - 1, 1) != '/' ) {
			$rutaDirectorio .= '/';
		}
		//if ( $prefijoFichero == '' )  $prefijoFichero = 'log_';

		// Ruta del fichero de log (excluyendo fecha y extensión)
		$this->rutaFicheroLog = $rutaDirectorio . $prefijoFichero;

		// Para el correcto funcionamiento de date()
		date_default_timezone_set('Europe/Madrid');

	}

	/*
	 *  método getRutaLog.
	 *
	 *  Obtiene la ruta del fichero de log de hoy.
	 *  
	 *  Parámetros: no tiene
	 */
	protected function getRutaLog () {

		return $this->rutaFicheroLog . date('Ymd');

	}

	/*
	 *  método notifica.
	 *
	 *  Realiza una notificación con los parámetros indicados
	 *  
	 *  Parámetros: $operacion: operación que se realiza
	 *              $usuario: usuario que realiza la operación
	 *              $tabla: tabla sobre la que se realiza la operación
	 *  Lanza excepción si: - no se puede abrir el fichero de log
	 */
	public function notifica ($operacion, $usuario, $tabla = '') {

		// Nombre completo del fichero de log
		$rutaLog = $this->getRutaLog();

		// Fichero de log
		$idFichero = fopen($rutaLog, 'a');
		if ( $idFichero === false )  throw new Excepcion("no se puede abrir el fichero '$rutaLog'", __METHOD__);

		// Línea del log
		$linea = '[' . date('H:i:s') . "] El usuario '$usuario' ha realizado un $operacion";
		if ( $tabla != '' )  $linea .= " sobre la tabla '$tabla'";
		$linea .= ".\r\n";
		fwrite($idFichero, $linea);
		fclose($idFichero);

	}

}

?>