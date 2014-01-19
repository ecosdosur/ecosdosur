<?php

/*
 * Clase Informe.
 *
 *  Representa las operaciones para la generaci�n de un informe de aplicaci�n.
 *   Hereda de ItemConectable.
 *
 *  IMPORTANTE: el m�dulo que incluye un objeto de esta clase (en principio, 'informe.php') ha de tener
 *    una serie de variables globales definidas para su correcto funcionamiento:
 *    - $_db (*): array con las diferentes conexiones del sistema y los par�metros de cada una de ellas, y
 *    - $_cnx (*): nombre de la conexi�n por defecto.
 *    - $_rutaLog: ruta de los ficheros de log de la aplicaci�n
 *   (*) Las dos primeras variables se usan en el m�todo getConector() de la clase base
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 08/01/2007
 */
class Informe extends ItemConectable {

	// Conector a la base de datos
	private $conector;

	// Librer�a del sistema
	private $libreriaSistema;

	// Tipo de informe: 'PDF' o 'Excel'
	private $tipoInforme;

	// Fuente del informe: 'informe' o 'pantalla'
	private $fuente;

	// C�digo del informe parametrizado en base de datos
	private $informe;

	// T�tulo del informe
	private $titulo;
	
	// Consulta para obtener los datos del informe
	private $selectResultado;

	// Vector con la configuraci�n de las columnas del informe
	private $arrayColumnas;

	// Constructor: inicializa variables
	public function __construct ($libreriaSistema, $arrayVariables) { 

		parent::__construct($arrayVariables);

		// Conexi�n a base de datos
		$this->conector = $this->getConector(false);

		// Librer�a del sistema
		$this->libreriaSistema = $libreriaSistema;

		// Obtenci�n de la parametrizaci�n de este informe y combinar con par�metros
		$this->preparaInforme();

	}

	/*
	 *  m�todo preparaInforme.
	 *
	 *  Obtiene la parametrizaci�n del informe y la combina con los par�metros de entrada.
	 *  
	 *  Par�metros: no tiene
	 *  Lanza excepci�n si: - error al obtener la parametrizaci�n
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

		// Variable con el c�digo del informe
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

		// Lector con la parametrizaci�n
		$lector = $this->getLectorParametrizacion();
		$lector->siguiente();

		// T�tulo
		$this->titulo = $lector->getValor('DESCRIPCION');

		// Consulta para el informe
		$this->selectResultado = $lector->getValor('SELECTRES');
		// - Condici�n
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
	 *  m�todo getLectorParametrizacion.
	 *
	 *  Obtiene el lector con la parametrizaci�n del informe, provenga este de informe o de pantalla
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: lector con la parametrizaci�n
	 *  Lanza excepci�n si: - error en base de datos
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
	 *  m�todo getColumnasInforme.
	 *
	 *  Obtiene las columnas del informe en el formato que reconocer�n los objetos generadores
	 *   del informe
	 *  
	 *  Par�metros: $lector: lector de base de datos
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
	 *  m�todo generaInforme.
	 *
	 *  Genera el informe y lo vuelca al flujo de salida. Ha de ser la �ltima llamada de su bloque
	 *  
	 *  Par�metros: no tiene
	 *  Lanza excepci�n si: - tipo de informe no v�lido
	 *                      - error en el volcado
	 */
	public function generaInforme () {

		// Instanciaci�n del objeto del informe: 4 posibilidades
		$objInforme = null;
		$fuente = ($this->informe == 'FICHERO' ? 'Fichero' : 'Lector');
		$nombreClase = $this->tipoInforme . 'Simple' . $fuente;
		$objInforme = new $nombreClase();

		// El fichero 'INFORME' es espec�fico
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

		// M�todos comunes (de la interfaz iInforme)
		$objInforme->setTitulo($this->titulo);
		$objInforme->setDescripcion($this->getVariable('descripcion'));
		$objInforme->vuelca();

	}

	
}

?>