<?php

/*
 * Clase FormateadorResultado.
 *
 *  Formatea el resultado de una consulta (lector) por una plantilla.
 *  Implementa las interfaces iJSON e iXML
 *  
 *  Autor: Pentared
 *  Última actualización: 08/01/2007
 */
class FormateadorResultado implements iJSON, iXML {

	// Atributos principales
	protected $lectorResultado;
	protected $plantilla;

	// Otros atributos
	protected $numResultados;
	protected $numFilas;
	protected $filaInicial;
	protected $error;
	protected $resultado;

	// Campos que se quieren en concreto
	protected $arrayCamposEspecificos;

	// XML resultado del formateo
	protected $xml;

	// Constructor por defecto
	public function __construct ($plantilla = '', $filaInicial = 0, $numFilas = -1) {

		$this->plantilla = $plantilla;
		$this->filaInicial = $filaInicial;
		$this->numFilas = $numFilas;
		$this->numResultados = 0;
		$this->error = $this->resultado = '';
		$this->lectorResultado = null;
		$this->xml = null;

		// Si no se definen campos específicos van todos los campos de la consulta
		$this->arrayCamposEspecificos = null;

	}

	// Destructor
	public function __destruct () { }

	// Métodos protegidos
	/*
	 *  método getPlantillaDefecto.
	 *
	 *  Genera una plantilla por defecto para el formateo del resultado. Ésta será una fila
	 *   de una tabla HTML.
	 *  
	 *  Parámetros: $lectorResultado: lector con el resultado de una consulta
	 *  Devuelve: resultado del formateo
	 *  Lanza excepción si: - error al usar el lector
	 */
	protected function getPlantillaDefecto ($arrayColumnas) {

		/*
		$plantillaDefecto = '<tr>';
		foreach ( $arrayColumnas as $columna ) {
			$plantillaDefecto .= '<td>{$'. $columna . '}</td>';
		}
		$plantillaDefecto .= '</tr>';
		*/
		$plantillaDefecto = '<registro>';
		foreach ( $arrayColumnas as $columna ) {
			$plantillaDefecto .= "<{$columna}>{\$$columna}</{$columna}>";
		}
		$plantillaDefecto .= '</registro>';

		return $plantillaDefecto;

	}

	// Métodos de acceso a los atributos
	// - lectura
	//public function getLectorResultado () { return $this->lectorResultado; }
	public function getPlantilla () { return $this->plantilla; }
	public function getNumResultados () { return $this->numResultados; }
	public function getNumFilas () { return $this->numFilas; }
	public function getFilaInicial () { return $this->filaInicial; }
	public function getError () { return $this->error; }
	public function getCamposEspecificos () { return $this->arrayCamposEspecificos; }
	// - escritura
	public function setPlantilla ($plantilla) { $this->plantilla = $plantilla; }
	public function setNumFilas ($numFilas) { $this->numFilas = $numFilas; }
	public function setFilaInicial ($filaInicial) { $this->filaInicial = $filaInicial; }
	public function setError ($error) { $this->error = $error; }
	public function setLectorResultado ($lectorResultado) { 
		$this->lectorResultado = $lectorResultado; 
		$this->xml = null;
	}
	public function setCamposEspecificos ($arrayCamposEspecificos) { 
		$this->arrayCamposEspecificos = $arrayCamposEspecificos;
	}
	
	/*
	 *  método getXML.
	 *
	 *  Convierte este objeto en un documento XML (XMLSimple).
	 *   Este XML contiene un único nodo con una serie de atributos y un contenido.
	 *   Este método pertenece a la interfaz iXML.
	 *
	 *  El XML generado tiene inicialmente como etiqueta del nodo raíz 'resultado-consulta' y 
	 *   visible el atributo de error.
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: un objeto XMLSimple
	 *  Lanza excepción si: - error al general el XML
	 */
	public function getXML () {

		// Si ya se ejecutó, no lo repetimos
		if ( !is_null($this->xml) )  return $this->xml;

		// Protección contra datos insuficientes
		if ( is_null($this->lectorResultado) )  throw new Excepcion('falta el lector', __METHOD__);

		// Plantilla
		//if ( $this->plantilla == '' )  $this->plantilla = $this->getPlantillaDefecto($arrayColumnas);
		//echo $this->plantilla;

		// Valores generales
		$this->resultado = '';
		//$this->numFilas = 0;
		$this->numResultados = $this->lectorResultado->getNumRegistros();

		// Si estamos fuera de rango, salimos
		if ( ($this->numResultados == 0) || ($this->filaInicial >= $this->numResultados) ) {
			$this->numFilas = 0;
			//return '';
		}
		if ( $this->numFilas == -1 )  $this->numFilas = $this->numResultados;

		// Contadores
		$filaIni = $this->filaInicial;
		$filaFin = min($this->numResultados, $filaIni + $this->numFilas);
		$this->numFilas = $filaFin - $filaIni;

		// Objeto XML: será visible el error
		$this->xml = new XMLSimple('resultado-consulta', true);
		$this->xml->setError($this->error);
		$this->xml->setContenido($this->resultado);
		$this->xml->addVariable('query', $this->lectorResultado->getQuery());
		$this->xml->addVariable('numResultados', $this->numResultados);
		$this->xml->addVariable('numFilas', $this->numFilas);
		$this->xml->addVariable('filaInicial', $this->filaInicial);
		$this->xml->addVariable('campos', implode(',', $this->lectorResultado->getColumnas()));

		// Formateo
		if ( $this->numResultados > 0 ) {
			$interpreteAux = new Interprete();
			$this->resultado = '';
			$this->lectorResultado->situarEnRegistro($filaIni);
			for ( $k = $filaIni; $k < $filaFin; $k++ ) {
				$this->lectorResultado->siguiente();
				$registro = $this->lectorResultado->getRegistro();
				if ( $this->plantilla == '' ) {
					$xmlNodo = new XMLSimple('registro', false);
					foreach ( $registro as $campo=>$valor ) {
						// Si no hay definidos campos específicos, vuelca todos en el XML
						if ( is_null($this->arrayCamposEspecificos) || in_array($campo, $this->arrayCamposEspecificos) ) {
							$xmlNodo->addVariable($campo, $valor);
						}
					}
					$this->xml->addNodo($xmlNodo);
				}
				else {
					//$this->resultado .= Interprete::interpreta($this->plantilla, $registro);
					// Pre-formateo para compatibilidad con HTML
					foreach ( $registro as $clave => $valor ) {
						//$valor = str_replace("'", '&#39;', $valor);
						$registro[$clave] = str_replace('"', '&quot;', $valor);
					}
					// Se añade la variable indicadora de registro: $_numRegistro
					$registro['_numRegistro'] = $k;
					$this->resultado .= $interpreteAux->interpreta($this->plantilla, $registro);
				}
			}
			$this->xml->setContenido($this->resultado);
		}

		// Devolvemos el resultado del formateo
		return $this->xml;

	}

	/*
	 *  método toJSON.
	 *
	 *  Método que implementa la interfaz iJSON. Para el caso en que el lector
	 *   contenido en la clase tenga definidas las columnas 'CODIGO' y 'DESCRIPCION', 
	 *   este método creará un array con cada entrada estos pares campo-valor.
	 *
	 *  NOTA: Por ahora ignora el número de filas y la fila inicial del objeto.
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: resultado del formateo
	 *  Lanza excepción si: - error al usar el lector
	 */
	public function toJSON () {

		// Protección contra datos insuficientes
		if ( is_null($this->lectorResultado) )  throw new Excepcion('falta el lector', __METHOD__);

		// Array con los nombres de las variables
		$arrayColumnas = $this->lectorResultado->getColumnas();
		if ( !in_array('CODIGO', $arrayColumnas) || !in_array('DESCRIPCION', $arrayColumnas) ) {
			throw new Excepcion('Los nombres de columnas no son los esperados', __METHOD__);
		}

		// Creamos el array que después convertiremos a JSON
		$arrayConsulta = array();
		$this->lectorResultado->situarEnRegistro(0);
		while ( $this->lectorResultado->siguiente() ) {
			$registro = $this->lectorResultado->getRegistro();
			$arrayConsulta[$registro['CODIGO']] = $registro['DESCRIPCION'];
		}

		// Conversión a JSON
		return JSONUtils::simpleArrayToJSON($arrayConsulta);

	}

}

?>
