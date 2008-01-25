<?php

/*
 * Clase Excepcion.
 *
 *  Representa una excepción.
 *  Hereda de Exception.
 *
 *  Autor: Pentared
 *  Última actualización: 16/01/2007
 */
class Excepcion extends Exception {

	// Atributos
	protected $metodo;

	// Constructor: su llamada afectará al mensaje que se recupere
	public function __construct ($mensaje, $metodo = '') {

		// Llamada al constructor de la clase base
		parent::__construct($mensaje);

		// Asignación del atributo
		$this->metodo = trim($metodo);

		// Añadimos la definición del método (si existe)
		if ( $this->metodo != '' )  $this->message = $metodo . '(): ' . $this->message;

		// Volcamos el error a log
		$this->toLog();

	}

	// Métodos de acceso a atributos
	public function getMetodo () { return $this->metodo; }
	public function setMetodo ($metodo) { $this->metodo = trim($metodo); }

	/*
	 *  método toLog.
	 *
	 *  Envía al fichero de log (si procede) el mensaje de la excepción
	 *  
	 *  Parámetros: no tiene
	 */
	protected function toLog () {

		// Variable de entorno
		global $_ap;

		// En esta variable global indicamos si queremos volcado en log de estos errores
		global $_logErroresAp;

		if ( isset($_logErroresAp) && $_logErroresAp ) {

			date_default_timezone_set('Europe/Madrid');
			$fichero = '../log/'. $_ap .'-excepcion-' . date('Ymd') . '.log';

			$ddf = fopen($fichero, 'a');
			fwrite($ddf,"[". date('H:i:s') ."] {$this->message}\r\n");
			fclose($ddf); 

		}

	}

}

?>