<?php

/*
 * Clase ExcelSimple.
 *
 *  Representa las operaciones básicas para obtener un documento Excel en PHP.
 *   Implementa iInforme e iFactoria.
 *
 *  Autor: Pentared
 *  Última actualización: 29/01/2007
 */
abstract class ExcelSimple implements iInforme, iFactoria {

	// Título del documento Excel
	protected $titulo;

	// Descripción del documento
	protected $descripcion;

	// Constructor de la clase: inicialización de variables
	public function __construct () {

		$this->titulo = '';
		$this->descripcion = '';

	}

	/*
	 *  método getInstancia.
	 *
	 *  Crea un objeto ExcelSimple según el tipo seleccionado.
	 *  
	 *  Parámetros: $tipo: tipo de documento Excel
	 *  Devuelve: un objeto ExcelSimple del tipo que se ha determinado
	 *  Lanza excepción si: - no se encuentra el tipo seleccionado.
	 */
	public static final function getInstancia ($tipo) {

		$tipo = trim($tipo);
		$nombreClase = 'ExcelSimple' . $tipo;

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

		// Cabecera
		header('Content-Type: application/vnd.ms-excel');
		//header("Content-type: application/x-msdownload"); 
		header('Content-Disposition: attachment;filename=' . $this->titulo . '.xls');

		// Título
		$this->nuevaLinea();
		$this->celda($this->titulo, true);
		$this->nuevaLinea();

		// Descripción
		if ( $this->descripcion != '' ) {
			$this->celda($this->descripcion, true);
			$this->nuevaLinea();
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

		// ...

	}

	/*
	 *  método celda.
	 *
	 *  Vuelca una nueva celda al flujo de salida con el contenido indicado
	 *  
	 *  Parámetros: $contenido: contenido a volcar en una celda
	 *              $bNuevaLinea: indica si hay que crear una nueva línea a continuación
	 */
	protected function celda ($contenido, $bNuevaLinea = false) {

		echo "$contenido";
		if ( $bNuevaLinea )  echo "\n";
		else echo "\t";

	}

	/*
	 *  método nuevaLinea.
	 *
	 *  Pasa a una nueva línea del documento Excel
	 *  
	 *  Parámetros: no tiene
	 */
	protected function nuevaLinea () {

		echo "\n";

	}

}

?>