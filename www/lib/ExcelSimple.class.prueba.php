<?php

/*
 * Clase ExcelSimple.
 *
 *  Representa las operaciones b�sicas para obtener un documento Excel en PHP.
 *   Implementa iInforme e iFactoria.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 09/01/2007
 */
abstract class ExcelSimple implements iInforme, iFactoria {

	// T�tulo del documento Excel
	protected $titulo;

	// Descripci�n del documento
	protected $descripcion;

	// Constructor de la clase: inicializaci�n de variables
	public function __construct () {

		$this->titulo = '';
		$this->descripcion = '';

	}

	/*
	 *  m�todo getInstancia.
	 *
	 *  Crea un objeto ExcelSimple seg�n el tipo seleccionado.
	 *  
	 *  Par�metros: $tipo: tipo de documento Excel
	 *  Devuelve: un objeto ExcelSimple del tipo que se ha determinado
	 *  Lanza excepci�n si: - no se encuentra el tipo seleccionado.
	 */
	public static final function getInstancia ($tipo) {

		$tipo = trim($tipo);
		$nombreClase = 'ExcelSimple' . $tipo;

		if ( ($tipo == '') || !include_once($nombreClase . '.class.php') ) {
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

		// Cabecera
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename=' . $this->titulo . '.xls');
		//header("Content-Disposition: inline;");
		//header('Content-Disposition: attachment;filename=fichero.xls');

		// Comienzo
		echo '<table border="1"><tr>';

		// T�tulo
		$this->nuevaLinea();
		$this->celda($this->titulo, true, 4);
		$this->nuevaLinea();

		// Descripci�n
		if ( $this->descripcion != '' ) {
			$this->celda($this->descripcion, true, 4);
			$this->nuevaLinea();
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

		// ...
		echo '</tr></table>';

	}

	/*
	 *  m�todo celda.
	 *
	 *  Vuelca una nueva celda al flujo de salida con el contenido indicado
	 *  
	 *  Par�metros: $contenido: contenido a volcar en una celda
	 *              $bNuevaLinea: indica si hay que crear una nueva l�nea a continuaci�n
	 */
	protected function celda ($contenido, $bNuevaLinea = false, $colspan = 1) {

		echo "<td colspan='$colspan'>$contenido</td>";
		/* if ( $bNuevaLinea )  echo "\n";
		else echo "\t"; */
		if ( $bNuevaLinea )  $this->nuevaLinea();

	}

	/*
	 *  m�todo nuevaLinea.
	 *
	 *  Pasa a una nueva l�nea del documento Excel
	 *  
	 *  Par�metros: no tiene
	 */
	protected function nuevaLinea () {

		//echo "\n";
		echo '</tr><tr>';

	}

}

?>