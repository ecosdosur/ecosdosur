<?php

/*
 * Clase ExcelSimpleLector.
 *
 *  Representa las operaciones para obtener un documento Excel en PHP partiendo
 *   de un lector a una operaci�n de consulta a base de datos.
 *   Hereda de ExcelSimple.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 09/01/2007
 */
class ExcelSimpleLector extends ExcelSimple {

	// Lector del resultado de la operaci�n a base de datos
	protected $lector;

	// Array con la configuraci�n de las columnas
	protected $columnas;

	// Constructor: inicializa variables
	public function __construct () {

		parent::__construct();

		$this->lector = null;
		$this->columnas = null;

	}

	/*
	 *  m�todo setLector.
	 *
	 *  Asigna el lector al resultado de una operaci�n de base de datos.
	 *  
	 *  Par�metros: $lector: lector al resultado de una operaci�n de base de datos.
	 */
	public function setLector ($lector) {

		$this->lector = $lector;
		$this->lector->setConversorTipos(ConversorTipos::getInstancia('ES'));

	}

	/*
	 *  m�todo setColumnas.
	 *
	 *  Asigna el vector con la configuraci�n de las columnas del documento Excel
	 *  
	 *  Par�metros: $columnas: vector con la configuraci�n de las columnas del documento Excel
	 */
	public function setColumnas ($columnas) {

		$this->columnas = $columnas;

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

		// No volcamos nada si no se defini� ning�n lector
		if ( is_null($this->lector) )  return;

		// N�mero de resultados
		$numResultados = $this->lector->getNumRegistros();

		// Total de resultados
		$this->celda("Total resultados: $numResultados", true);
		$this->nuevaLinea();

		// Cabecera de las columnas
		$arrayColumnas = null;
		if ( !is_null($this->columnas) ) {
			foreach ( $this->columnas as $arrayColumna ) {
				$arrayColumnas[] = $arrayColumna['nombre'];
				$this->celda($arrayColumna['descripcion']);
			}
			$this->nuevaLinea();
		}

		// Datos
		if ( !is_null($this->lector) ) {
			if ( is_null($arrayColumnas) )  $arrayColumnas = $this->lector->getColumnas();
			while ( $this->lector->siguiente() ) {
				$registro = $this->lector->getRegistro();
				foreach ( $arrayColumnas as $columna ) {
					$this->celda($registro[$columna]);
				}
				$this->nuevaLinea();
			}
		}

	}

}

?>