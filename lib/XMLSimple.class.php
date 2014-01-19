<?php

/*
 * Clase XMLSimple.
 *
 *  Representa un XML con un �nico nodo, un n�mero indeterminado de atributos, contenido 
 *   y un array de nodos hijos.
 *  Hereda de Contenedor. Este tipo de objeto ser� el que se tenga que devolver en el m�todo
 *   getXML() para aquellas clases que implementen la interfaz iXML.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 31/08/2007
 */
class XMLSimple extends Contenedor implements iJSON {

	// Etiqueta del nodo ra�z
	protected $etiqueta;

	// Indica si es nodo ra�z
	protected $bNodoRaiz;

	// Mensaje de error
	protected $bErrorVisible;
	protected $error;

	// Contenido del nodo ra�z
	protected $contenido;

	// Array de objetos XMLSimple
	protected $arrayNodos;

	// Constructor
	public function __construct ($etiqueta = '', $bErrorVisible = false) { 

		// Llamada al constructor de la clase base
		parent::__construct();

		// Inicializaci�n de los atributos
		$this->error = $this->contenido = '';
		$this->etiqueta = $etiqueta;
		$this->bNodoRaiz = true;
		$this->bErrorVisible = $bErrorVisible;
		$this->arrayNodos = array();

	}

	/*
	 *  m�todo parseAtributo.
	 *
	 *  Elimina caracteres conflictivos para un atributo en un documento XML
	 *  
	 *  Par�metros: $atributo: atributo a filtrar
	 *  Devuelve: cadena con los caracteres conflictivos traducidos
	 */
	protected function parseAtributo ($atributo) {

		$atributo = str_replace('"', '&quot;', $atributo);
		$atributo = str_replace('<', '&lt;', $atributo);
		$atributo = str_replace('>', '&gt;', $atributo);

		return $atributo;

	}

	// M�todos de acceso y obtenci�n
	public function getEtiqueta () { return $this->etiqueta; }
	public function getError () { return $this->error; }
	public function getErrorVisible () { return $this->bErrorVisible; }
	public function getContenido () { return $this->contenido; }
	public function setEtiqueta ($etiqueta) { $this->etiqueta = trim($etiqueta); }
	public function setError ($error) { $this->error = $error; }
	public function setErrorVisible ($bErrorVisible) { $this->bErrorVisible = $bErrorVisible; }
	public function setNodoRaiz ($bNodoRaiz) { $this->bNodoRaiz = $bNodoRaiz; }
	public function setContenido ($contenido) { $this->contenido = $contenido; }

	
	/*
	 *  m�todo addNodo.
	 *
	 *  A�ade un nodo hijo a este documento XML. Todos los nodos a�adidos deben tener
	 *   la misma etiqueta ra�z.
	 *  
	 *  Par�metros: $nodo: objeto XMLSimple
	 *  Lanza excepci�n si: - no est� cubierta la etiqueta
	 *                      - la etiqueta no es v�lida (coincide con la del nodo ra�z)
	 *                      - la etiqueta no es v�lida (no coincide con la ya existente)
	 */
	public function addNodo ($nodo) { 

		// Controles de etiqueta no v�lida
		$etiqueta = $nodo->getEtiqueta();
		if ( $etiqueta == '' )  throw new Excepcion('la etiqueta est� vac�a', __METHOD__);
		if ( $etiqueta == $this->etiqueta ) {
			throw new Excepcion('la etiqueta coincide con la del nodo ra�z', __METHOD__);
		}
		if ( sizeof($this->arrayNodos) > 0 ) {
			if ( $this->arrayNodos[0]->getEtiqueta() != $etiqueta ) {
				throw new Excepcion('la etiqueta coincide con la de su(s) hermano(s)', __METHOD__);
			}
		}

		// Si no hay problema. se a�ade el nodo (se fuerza a no ser ra�z)
		$nodo->setNodoRaiz(false);
		$this->arrayNodos[] = $nodo; 

	}

	/*
	 *  m�todo getString.
	 *
	 *  Convierte este objeto en una cadena que representa el documento XML.
	 *   Este XML contiene un nodo ra�z con una serie de atributos y, recursivamente,
	 *   lo mismo con sus nodos hijos
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: cadena que representa el documento XML
	 *  Lanza excepci�n si: - no est� cubierta la etiqueta
	 */
	public function getString () { 

		if ( $this->etiqueta == '' )  throw new Excepcion('Es necesaria etiqueta', __METHOD__);

		// Etiqueta del nodo ra�z
		$xml = '<' . $this->etiqueta;

		// Atributos del nodo ra�z: 'error', definidos por el usuario y 'contenido'
		if ( $this->bErrorVisible )  $xml .= ' error="' . $this->parseAtributo($this->error) . '"';
		foreach ( $this->arrayVariables as $clave=>$valor ) {
			$xml .= ' ' . $clave . '="' . $this->parseAtributo($valor) . '"';
		}
		$xml .= ' contenido="' . $this->parseAtributo($this->contenido) . '">';

		// Nodos hijos
		foreach ( $this->arrayNodos as $nodo ) {
			$xml .= $nodo->getString();
		}

		// Cierre de la etiqueta
		$xml .= '</' . $this->etiqueta . '>';

		return $xml;

	}

	/*
	 *  m�todo iJSON.
	 *
	 *  Convierte este objeto en una cadena que representa un objeto JavaScript
	 *   Este m�todo proviene de implementar la interfaz iJSON.
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: cadena que representa el documento XML como objeto JavaScript.
	 *  Lanza excepci�n si: - no est� cubierta la etiqueta
	 */
	public function toJSON () {

		if ( $this->etiqueta == '' )  throw new Excepcion('Es necesaria etiqueta', __METHOD__);

		// "Nodo ra�z"
		$json = '{';
		if ( $this->bNodoRaiz ) $json .= '"' . str_replace('-', '_', $this->etiqueta) . '": {';

		// Atributos: 'error', definidos por el usuario y 'contenido'
		if ( $this->bErrorVisible )  $json .= '"error": "' . str_replace('"', '\"', $this->error) . '", ';
		foreach ( $this->arrayVariables as $variable=>$valor ) {
			//$valor = str_replace("\r", ' ', $valor);
			//$valor = str_replace("\n", ' ', $valor);
			$valor = str_replace("\r", '\r', $valor);
			$valor = str_replace("\n", '\n', $valor);
			$json .= '"' . $variable . '": "' . str_replace('"', '\"', $valor) . '", ';
		}
		$json .= '"contenido": "' . str_replace('"', '\"', $this->contenido) . '"';

		// Nodos hijos (si los tiene)
		if ( sizeof($this->arrayNodos) > 0 ) {
			$etiqueta = $this->arrayNodos[0]->getEtiqueta();
			$json .= ', "' . $etiqueta . '": [';
			$arrayNodosJSON = array();
			foreach ( $this->arrayNodos as $nodo ) {
				$arrayNodosJSON[] = $nodo->toJSON();
			}
			$json .= implode(', ', $arrayNodosJSON);
			$json .= ']';
		}

		// Cierre del "nodo ra�z"
		$json .= '}';
		if ( $this->bNodoRaiz )  $json .= '}';

		return $json;

	}

}

?>