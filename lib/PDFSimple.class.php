<?php

/*
 * Clase PDFSimple.
 *
 *  Representa las operaciones b�sicas para obtener un documento PDF en PHP.
 *   Hereda de Cepdf e implementa iInforme.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 29/01/2007
 */
abstract class PDFSimple extends Cezpdf implements iInforme, iFactoria {

	// Ancho por defecto del documento PDF
	const anchoDefecto = 500;

	// T�tulo del documento PDF
	protected $titulo;

	// Descripci�n del documento
	protected $descripcion;

	// Constructor de la clase: par�metros por defecto a Cezpdf
	public function __construct () {

		parent::__construct();
		$this->titulo = '';
		$this->descripcion = '';

	}

	/*
	 *  m�todo getInstancia.
	 *
	 *  Crea un objeto PDFSimple seg�n el tipo seleccionado.
	 *  
	 *  Par�metros: $tipo: tipo de documento PDF
	 *  Devuelve: un objeto PDFSimple del tipo que se ha determinado
	 *  Lanza excepci�n si: - no se encuentra el tipo seleccionado.
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
	 *  m�todo setTitulo.
	 *
	 *  Asigna el t�tulo del documento. Este m�todo pertenece a la interfaz iInforme.
	 *  
	 *  Par�metros: $titulo: t�tulo del documento
	 */
	public function setTitulo ($titulo) {

		$this->titulo = $titulo;

	}

	/*
	 *  m�todo setDescripcion.
	 *
	 *  Asigna la descripci�n del documento. Este m�todo pertenece a la interfaz iInforme.
	 *  
	 *  Par�metros: $descripcion:  descripci�n del documento
	 */
	public function setDescripcion ($descripcion) {

		$this->descripcion = $descripcion;

	}

	/*
	 *  m�todo vuelca.
	 *
	 *  Realiza el volcado del documento. Ha de ser la �ltima l�nea a ejecutar
	 *   del bloque llamante. Este m�todo pertenece a la interfaz iInforme.
	 *  
	 *  Par�metros: no tiene
	 *  Lanza excepci�n si: - error en el volcado
	 */
	public function vuelca () {

		// Acciones iniciales comunes
		$this->inicioVolcado();

		// Llamada a la funci�n que tiene que instanciar la clase hija
		$this->_vuelca();

		// Acciones finales comunes: volcado
		$this->finVolcado();

	}

	/*
	 *  m�todo inicioVolcado.
	 *
	 *  Realiza las operaciones comunes previas al volcado del documento PDF.
	 *  
	 *  Par�metros: no tiene
	 *  Lanza excepci�n si: - error en el volcado
	 */
	protected function inicioVolcado () {

		// Par�metros generales
		$this->selectFont('../lib/fonts/Helvetica.afm');
		$this->ezSetMargins(30, 60, 30, 30);

		// Pie de p�gina: numeraci�n
		$this->ezStartPageNumbers(325, 30, 12, '', '<i>P�gina {PAGENUM} de {TOTALPAGENUM}</i>');

		// T�tulo
		if ( $this->titulo != '' ) {
			$this->ezText('<b>' . $this->titulo . '</b>', 15, array('left'=>10));
			$this->ezText('', 15);
		}

		// Descripci�n
		if ( $this->descripcion != '' ) {
			$this->ezText('<i>' . $this->descripcion . '</i>', 13, array('left'=>10));
			$this->ezText('', 15);
		}

	}

	/*
	 *  m�todo _vuelca.
	 *
	 *  M�todo que han de instanciar las clases que hereden de �sta para volcar
	 *   adecuadamente su contenido.
	 *  
	 *  Par�metros: no tiene
	 *  Lanza excepci�n si: - error en el volcado
	 */
	abstract protected function _vuelca ();

	/*
	 *  m�todo finVolcado.
	 *
	 *  Realiza las operaciones finales comunes del volcado del documento PDF.
	 *  
	 *  Par�metros: no tiene
	 *  Lanza excepci�n si: - error en el volcado
	 */
	protected function finVolcado () {

		$this->ezStream();

	}

}

?>