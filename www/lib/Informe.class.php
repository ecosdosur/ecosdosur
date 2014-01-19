<?php

/*
 * Clase Informe.
 *
 *  Representa las operaciones para la generación de un informe de aplicación.
 *   Hereda de ItemConectable.
 *
 *  IMPORTANTE: el módulo que incluye un objeto de esta clase (en principio, 'informe.php') ha de tener
 *    una serie de variables globales definidas para su correcto funcionamiento:
 *    - $_db (*): array con las diferentes conexiones del sistema y los parámetros de cada una de ellas, y
 *    - $_cnx (*): nombre de la conexión por defecto.
 *    - $_rutaLog: ruta de los ficheros de log de la aplicación
 *   (*) Las dos primeras variables se usan en el método getConector() de la clase base
 *
 *  Autor: Pentared
 *  Última actualización: 08/01/2007
 */
class Informe extends ItemConectable {

	// Conector a la base de datos
	private $conector;

	// Librería del sistema
	private $libreriaSistema;

	// Tipo de informe: 'PDF' o 'Excel'
	private $tipoInforme;

	// Fuente del informe: 'informe' o 'pantalla'
	private $fuente;

	// Código del informe parametrizado en base de datos
	private $informe;

	// Título del informe
	private $titulo;
	
	// Consulta para obtener los datos del informe
	private $selectResultado;

	// Vector con la configuración de las columnas del informe
	private $arrayColumnas;

	// Constructor: inicializa variables
	public function __construct ($libreriaSistema, $arrayVariables) { 

		parent::__construct($arrayVariables);

		// Conexión a base de datos
		$this->conector = $this->getConector(false);

		// Librería del sistema
		$this->libreriaSistema = $libreriaSistema;

		// Obtención de la parametrización de este informe y combinar con parámetros
		$this->preparaInforme();

	}

	/*
	 *  método preparaInforme.
	 *
	 *  Obtiene la parametrización del informe y la combina con los parámetros de entrada.
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - error al obtener la parametrización
	 */
	private function preparaInforme() {

		// Variable con el tipo de informe: PDF, Excel...
		if ( !$this->hayVariable('tipo') ) {
			throw new Excepcion("no se ha pasado la variable 'tipo'", __METHOD__);
		}
		$this->tipoInforme = $this->getVariable('tipo');

		// Variable con la fuente del informe: informe, pantalla,...
		if ( !$this->hayVariable('fuente') ) {
			throw new Excepcion("no se ha pasado la variable 'fuente'", __METHOD__);
		}
		$this->fuente = $this->getVariable('fuente');

		// Variable con el código del informe
		if ( !$this->hayVariable('informe') ) {
			throw new Excepcion("no se ha pasado la variable 'informe'", __METHOD__);
		}
		$this->informe = $this->getVariable('informe');

		// Informe especial: fichero
		if ( $this->informe == 'FICHERO' ) {
			if ( !$this->hayVariable('ruta') )  throw new Excepcion("falta la variable 'ruta'", __METHOD__);
			$this->titulo = $this->getVariable('titulo');
			return;
		}

		// Lector con la parametrización
		$lector = $this->getLectorParametrizacion();
		$lector->siguiente();

		// Título
		$this->titulo = $lector->getValor('DESCRIPCION');

		// Consulta para el informe
		$this->selectResultado = $lector->getValor('SELECTRES');
		// - Condición
		$condicion = $this->getVariable('condicion');
		if ( $condicion != '' ) {
			if ( stripos($this->selectResultado, 'WHERE') === false ) {
				$this->selectResultado .= ' WHERE ';
			}
			else {
				$this->selectResultado .= ' AND ';
			}
			$this->selectResultado .= $condicion;
		}
		// - Orden
		$orden = $this->getVariable('orden');
		if ( $orden != '' ) {
			$this->selectResultado .= ' ORDER BY ' . $orden;
		}

		// Array de columnas
		$this->getColumnasInforme($lector);

	}

	/*
	 *  método getLectorParametrizacion.
	 *
	 *  Obtiene el lector con la parametrización del informe, provenga este de informe o de pantalla
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: lector con la parametrización
	 *  Lanza excepción si: - error en base de datos
	 *                      - no existe el informe
	 */
	private function getLectorParametrizacion () {

		$tabla = ($this->fuente == 'informe' ? 'informes' : 'pantallas');
		$codigo = ($this->fuente == 'informe' ? 'CODINFORME' : 'PANTALLA');
		$consulta = "SELECT * FROM {$this->libreriaSistema}.{$tabla} WHERE {$codigo}='{$this->informe}'";
		$lector = $this->conector->consulta($consulta);
		if ( $lector->getNumRegistros() == 0 ) {
			throw new Excepcion("no existe el informe '{$this->informe}'", __METHOD__);
		}

		return $lector;

	}

	/*
	 *  método getColumnasInforme.
	 *
	 *  Obtiene las columnas del informe en el formato que reconocerán los objetos generadores
	 *   del informe
	 *  
	 *  Parámetros: $lector: lector de base de datos
	 */
	private function getColumnasInforme ($lector) {

		$columnas = $lector->getValor('CAMPOSRES');
		$arrayColumnas = explode(' ', $columnas);
		$this->arrayColumnas = array();
		foreach ( $arrayColumnas as $columna ) {
			$arrayColumna = explode('|', $columna);
			if ( $this->fuente == 'informe' ) {
				$this->arrayColumnas[] = array(
					'nombre'=>$arrayColumna[0], 
					'descripcion'=>str_replace('_', ' ', $arrayColumna[1]), 
					'tipo'=>$arrayColumna[2], 
					'ancho'=>$arrayColumna[3]
					);
			}
			else {
				$this->arrayColumnas[] = array(
					'nombre'=>$arrayColumna[1], 
					'descripcion'=>str_replace('_', ' ', $arrayColumna[2]), 
					'tipo'=>str_replace('#', '', $arrayColumna[3]), 
					'ancho'=>$arrayColumna[4]
					);
			}
		}

	}

	/*
	 *  método generaInforme.
	 *
	 *  Genera el informe y lo vuelca al flujo de salida. Ha de ser la última llamada de su bloque
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - tipo de informe no válido
	 *                      - error en el volcado
	 */
	public function generaInforme () {

		// Instanciación del objeto del informe: 4 posibilidades
		$objInforme = null;
		$fuente = ($this->informe == 'FICHERO' ? 'Fichero' : 'Lector');
		$nombreClase = $this->tipoInforme . 'Simple' . $fuente;
		$objInforme = new $nombreClase();

		// El fichero 'INFORME' es específico
		if ( $this->informe == 'FICHERO' ) {
			global $_rutaLog;
			if ( !isset($_rutaLog) )  throw new Excepcion('Variable global $_rutaLog no inicializada', __METHOD__);
			$rutaFichero = $_rutaLog . '/' . $this->getVariable('ruta');
			$objInforme->setFichero($rutaFichero);
		}
		else {
			$lector = $this->conector->consulta($this->selectResultado);
			$objInforme->setColumnas($this->arrayColumnas);
			$objInforme->setLector($lector);
		}

		// Métodos comunes (de la interfaz iInforme)
		$objInforme->setTitulo($this->titulo);
		$objInforme->setDescripcion($this->getVariable('descripcion'));
		$objInforme->vuelca();

	}

	
}

?>