<?php

/*
 * Clase PDFSimpleLector.
 *
 *  Representa las operaciones para obtener un documento PDF en PHP partiendo
 *   de un lector a una operaci�n de consulta a base de datos.
 *   Hereda de PDFSimple.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 09/01/2007
 */
class PDFSimpleLector extends PDFSimple {

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
	 *  Asigna el vector con la configuraci�n de las columnas del documento PDF
	 *  
	 *  Par�metros: $columnas: vector con la configuraci�n de las columnas del documento PDF
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

		// Matriz de datos
		$datos = array();
		while ( $this->lector->siguiente() ) {
			$datos[] = $this->lector->getRegistro();
		}

		// Columnas
		$columnas = ''; 
		$opcionesColumnas = '';
		if ( !is_null($this->columnas) ) {
			foreach ( $this->columnas as $arrayColumna ) {
				$columnas[$arrayColumna['nombre']] = '<i>' . $arrayColumna['descripcion'] . '</i> ';
				if ( $arrayColumna['ancho'] ) {
					$ancho = round($arrayColumna['ancho']*self::anchoDefecto/100);
					$opcionesColumnas[$arrayColumna['nombre']]['width'] = $ancho;
				}
				if ( $arrayColumna['tipo'] ) {
					switch ( $arrayColumna['tipo'] ) {
						case 'D': { $alineacion = 'right'; break; }
						case 'F': { $alineacion = 'center'; break; }
						default: { $alineacion = 'left'; }
					}
					$opcionesColumnas[$arrayColumna['nombre']]['justification'] = $alineacion;
				}
			}
		}

		// Opciones
		$opciones = array();
		$opciones['width'] = self::anchoDefecto;
		$opciones['showLines'] = 2;
		$opciones['lineCol'] = array(0.7, 0.7, 0.7);
		$opciones['shadeCol'] = array(0.9, 0.9, 0.9);
		$opciones['cols'] = $opcionesColumnas;

		// Volcado final
		$this->ezText("Total resultados: <b>$numResultados</b>", 12, array('left'=>10));
		$this->ezText('', 13);
		$this->ezTable($datos, $columnas, '', $opciones);

	}

}

?>