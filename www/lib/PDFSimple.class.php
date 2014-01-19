<?php

/*
 * Clase PDFSimple.
 *
 *  Representa las operaciones básicas para obtener un documento PDF en PHP.
 *   Hereda de Cepdf e implementa iInforme.
 *
 *  Autor: Pentared
 *  Última actualización: 29/01/2007
 */
abstract class PDFSimple extends Cezpdf implements iInforme, iFactoria {

	// Ancho por defecto del documento PDF
	const anchoDefecto = 500;

	// Título del documento PDF
	protected $titulo;

	// Descripción del documento
	protected $descripcion;

	// Constructor de la clase: parámetros por defecto a Cezpdf
	public function __construct () {

		parent::__construct();
		$this->titulo = '';
		$this->descripcion = '';

	}

	/*
	 *  método getInstancia.
	 *
	 *  Crea un objeto PDFSimple según el tipo seleccionado.
	 *  
	 *  Parámetros: $tipo: tipo de documento PDF
	 *  Devuelve: un objeto PDFSimple del tipo que se ha determinado
	 *  Lanza excepción si: - no se encuentra el tipo seleccionado.
	 */
	public static final function getInstancia ($tipo) {

		$tipo = trim($tipo);
		$nombreClase = 'PDFSimple' . $tipo;

		if ( ($tipo == '') || !autoCarga($nombreClase) ) {
			throw new Excepcion('Tipo no encontrado', __METHOD__);
		}

		return new $nombreClase();

	}

	/*
	 *  método setTitulo.
	 *
	 *  Asigna el título del documento. Este método pertenece a la interfaz iInforme.
	 *  
	 *  Parámetros: $titulo: título del documento
	 */
	public function setTitulo ($titulo) {

		$this->titulo = $titulo;

	}

	/*
	 *  método setDescripcion.
	 *
	 *  Asigna la descripción del documento. Este método pertenece a la interfaz iInforme.
	 *  
	 *  Parámetros: $descripcion:  descripción del documento
	 */
	public function setDescripcion ($descripcion) {

		$this->descripcion = $descripcion;

	}

	/*
	 *  método vuelca.
	 *
	 *  Realiza el volcado del documento. Ha de ser la última línea a ejecutar
	 *   del bloque llamante. Este método pertenece a la interfaz iInforme.
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - error en el volcado
	 */
	public function vuelca () {

		// Acciones iniciales comunes
		$this->inicioVolcado();

		// Llamada a la función que tiene que instanciar la clase hija
		$this->_vuelca();

		// Acciones finales comunes: volcado
		$this->finVolcado();

	}

	/*
	 *  método inicioVolcado.
	 *
	 *  Realiza las operaciones comunes previas al volcado del documento PDF.
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - error en el volcado
	 */
	protected function inicioVolcado () {

		// Parámetros generales
		$this->selectFont('../lib/fonts/Helvetica.afm');
		$this->ezSetMargins(30, 60, 30, 30);

		// Pie de página: numeración
		$this->ezStartPageNumbers(325, 30, 12, '', '<i>Página {PAGENUM} de {TOTALPAGENUM}</i>');

		// Título
		if ( $this->titulo != '' ) {
			$this->ezText('<b>' . $this->titulo . '</b>', 15, array('left'=>10));
			$this->ezText('', 15);
		}

		// Descripción
		if ( $this->descripcion != '' ) {
			$this->ezText('<i>' . $this->descripcion . '</i>', 13, array('left'=>10));
			$this->ezText('', 15);
		}

	}

	/*
	 *  método _vuelca.
	 *
	 *  Método que han de instanciar las clases que hereden de ésta para volcar
	 *   adecuadamente su contenido.
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - error en el volcado
	 */
	abstract protected function _vuelca ();

	/*
	 *  método finVolcado.
	 *
	 *  Realiza las operaciones finales comunes del volcado del documento PDF.
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - error en el volcado
	 */
	protected function finVolcado () {

		$this->ezStream();

	}

}

?>