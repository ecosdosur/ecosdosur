<?php

/*
 * Clase ExcelSimpleLector.
 *
 *  Representa las operaciones para obtener un documento Excel en PHP partiendo
 *   de un lector a una operación de consulta a base de datos.
 *   Hereda de ExcelSimple.
 *
 *  Autor: Pentared
 *  Última actualización: 09/01/2007
 */
class ExcelSimpleLector extends ExcelSimple {

	// Lector del resultado de la operación a base de datos
	protected $lector;

	// Array con la configuración de las columnas
	protected $columnas;

	// Constructor: inicializa variables
	public function __construct () {

		parent::__construct();

		$this->lector = null;
		$this->columnas = null;

	}

	/*
	 *  método setLector.
	 *
	 *  Asigna el lector al resultado de una operación de base de datos.
	 *  
	 *  Parámetros: $lector: lector al resultado de una operación de base de datos.
	 */
	public function setLector ($lector) {

		$this->lector = $lector;
		$this->lector->setConversorTipos(ConversorTipos::getInstancia('ES'));

	}

	/*
	 *  método setColumnas.
	 *
	 *  Asigna el vector con la configuración de las columnas del documento Excel
	 *  
	 *  Parámetros: $columnas: vector con la configuración de las columnas del documento Excel
	 */
	public function setColumnas ($columnas) {

		$this->columnas = $columnas;

	}

	/*
	 *  método _vuelca.
	 *
	 *  Vuelca el contenido del lector indicado anteriormente al documento PDF.
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - error en el volcado
	 */
	protected function _vuelca () {

		// No volcamos nada si no se definió ningún lector
		if ( is_null($this->lector) )  return;

		// Número de resultados
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