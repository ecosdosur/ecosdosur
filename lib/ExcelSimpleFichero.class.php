<?php

/*
 * Clase ExcelSimpleFichero.
 *
 *  Representa las operaciones para obtener un documento Excel en PHP 
 *   a partir del contenido de un fichero.
 *   Hereda de ExcelSimple.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 26/11/2006
 */
class ExcelSimpleFichero extends ExcelSimple {

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

		// Ruta del fichero
		$this->celda("Fichero: {$this->rutaFichero}", true);

		// Total de resultados
		$this->celda("Total entradas: $numResultados", true);
		$this->nuevaLinea();

		// Volcado del resto del fichero
		foreach ( $lineasFichero as $linea ) {
			$linea = str_replace("\r\n", '', $linea);
			$this->celda($linea, true);
		}


	}

}

?>