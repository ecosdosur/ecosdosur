<?php

/*
 * Clase Excepcion.
 *
 *  Representa una excepci�n.
 *  Hereda de Exception.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 16/01/2007
 */
class Excepcion extends Exception {

	// Atributos
	protected $metodo;

	// Constructor: su llamada afectar� al mensaje que se recupere
	public function __construct ($mensaje, $metodo = '') {

		// Llamada al constructor de la clase base
		parent::__construct($mensaje);

		// Asignaci�n del atributo
		$this->metodo = trim($metodo);

		// A�adimos la definici�n del m�todo (si existe)
		if ( $this->metodo != '' )  $this->message = $metodo . '(): ' . $this->message;

		// Volcamos el error a log
		$this->toLog();

	}

	// M�todos de acceso a atributos
	public function getMetodo () { return $this->metodo; }
	public function setMetodo ($metodo) { $this->metodo = trim($metodo); }

	/*
	 *  m�todo toLog.
	 *
	 *  Env�a al fichero de log (si procede) el mensaje de la excepci�n
	 *  
	 *  Par�metros: no tiene
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