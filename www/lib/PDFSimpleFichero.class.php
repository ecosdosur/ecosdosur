<?php

/*
 * Clase PDFSimpleFichero.
 *
 *  Representa las operaciones para obtener un documento PDF en PHP a partir de un fichero.
 *   Hereda de PDFSimple.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 26/11/2006
 */
class PDFSimpleFichero extends PDFSimple {

	// Ruta del fichero
	protected $rutaFichero;

	// Constructor: inicializa variables
	public function __construct () {

		parent::__construct();

		$this->rutaFichero = null;

	}

	/*
	 *  m�todo setFichero.
	 *
	 *  Asigna la ruta del fichero
	 *  
	 *  Par�metros: $rutaFichero: ruta del fichero.
	 */
	public function setFichero ($rutaFichero) {

		$this->rutaFichero = $rutaFichero;

	}

	/*
	 *  m�todo _vuelca.
	 *
	 *  Vuelca el contenido del lector indicado anteriormente al documento PDF.
	 *  
	 *  Par�metros: no tiene
	 *  Lanza excepci�n si: - error en el volcado
	 */
	protected function _vuelca () {

		// No volcamos nada si no se defini� ning�n fichero
		if ( is_null($this->rutaFichero) )  return;

		// Obtenemos el contenido del fichero
		if ( ($lineasFichero = file($this->rutaFichero)) === false ) {
			//throw new Excepcion("no se encuentra el fichero '{$this->rutaFichero}'", __METHOD__);
			$lineasFichero[] = '';
		}
		
		// N�mero de resultados
		$numResultados = sizeof($lineasFichero);

		// Matriz de datos
		$datos = array();
		foreach ( $lineasFichero as $linea ) {
			$datos[] = array('texto' => str_replace("\r\n", '', $linea));
		}
		unset($lineasFichero);

		// Opciones
		$opciones = array();
		$opciones['width'] = self::anchoDefecto;
		$opciones['showLines'] = 2;
		$opciones['lineCol'] = array(0.7, 0.7, 0.7);
		$opciones['shadeCol'] = array(0.9, 0.9, 0.9);
		//$opciones['cols'] = '';
		$opciones['showHeadings'] = 0;

		// Volcado final
		$this->ezText("Fichero: <b>{$this->rutaFichero}</b>", 13, array('left'=>10));
		$this->ezText('', 13);
		$this->ezText("Total entradas: <b>$numResultados</b>", 13, array('left'=>10));
		$this->ezText('', 13);
		$this->ezTable($datos,'', '', $opciones);

	}

}

?>