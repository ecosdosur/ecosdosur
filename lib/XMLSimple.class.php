<?php

/*
 * Clase XMLSimple.
 *
 *  Representa un XML con un único nodo, un número indeterminado de atributos, contenido 
 *   y un array de nodos hijos.
 *  Hereda de Contenedor. Este tipo de objeto será el que se tenga que devolver en el método
 *   getXML() para aquellas clases que implementen la interfaz iXML.
 *
 *  Autor: Pentared
 *  Última actualización: 31/08/2007
 */
class XMLSimple extends Contenedor implements iJSON {

	// Etiqueta del nodo raíz
	protected $etiqueta;

	// Indica si es nodo raíz
	protected $bNodoRaiz;

	// Mensaje de error
	protected $bErrorVisible;
	protected $error;

	// Contenido del nodo raíz
	protected $contenido;

	// Array de objetos XMLSimple
	protected $arrayNodos;

	// Constructor
	public function __construct ($etiqueta = '', $bErrorVisible = false) { 

		// Llamada al constructor de la clase base
		parent::__construct();

		// Inicialización de los atributos
		$this->error = $this->contenido = '';
		$this->etiqueta = $etiqueta;
		$this->bNodoRaiz = true;
		$this->bErrorVisible = $bErrorVisible;
		$this->arrayNodos = array();

	}

	/*
	 *  método parseAtributo.
	 *
	 *  Elimina caracteres conflictivos para un atributo en un documento XML
	 *  
	 *  Parámetros: $atributo: atributo a filtrar
	 *  Devuelve: cadena con los caracteres conflictivos traducidos
	 */
	protected function parseAtributo ($atributo) {

		$atributo = str_replace('"', '&quot;', $atributo);
		$atributo = str_replace('<', '&lt;', $atributo);
		$atributo = str_replace('>', '&gt;', $atributo);

		return $atributo;

	}

	// Métodos de acceso y obtención
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
	 *  método addNodo.
	 *
	 *  Añade un nodo hijo a este documento XML. Todos los nodos añadidos deben tener
	 *   la misma etiqueta raíz.
	 *  
	 *  Parámetros: $nodo: objeto XMLSimple
	 *  Lanza excepción si: - no está cubierta la etiqueta
	 *                      - la etiqueta no es válida (coincide con la del nodo raíz)
	 *                      - la etiqueta no es válida (no coincide con la ya existente)
	 */
	public function addNodo ($nodo) { 

		// Controles de etiqueta no válida
		$etiqueta = $nodo->getEtiqueta();
		if ( $etiqueta == '' )  throw new Excepcion('la etiqueta está vacía', __METHOD__);
		if ( $etiqueta == $this->etiqueta ) {
			throw new Excepcion('la etiqueta coincide con la del nodo raíz', __METHOD__);
		}
		if ( sizeof($this->arrayNodos) > 0 ) {
			if ( $this->arrayNodos[0]->getEtiqueta() != $etiqueta ) {
				throw new Excepcion('la etiqueta coincide con la de su(s) hermano(s)', __METHOD__);
			}
		}

		// Si no hay problema. se añade el nodo (se fuerza a no ser raíz)
		$nodo->setNodoRaiz(false);
		$this->arrayNodos[] = $nodo; 

	}

	/*
	 *  método getString.
	 *
	 *  Convierte este objeto en una cadena que representa el documento XML.
	 *   Este XML contiene un nodo raíz con una serie de atributos y, recursivamente,
	 *   lo mismo con sus nodos hijos
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: cadena que representa el documento XML
	 *  Lanza excepción si: - no está cubierta la etiqueta
	 */
	public function getString () { 

		if ( $this->etiqueta == '' )  throw new Excepcion('Es necesaria etiqueta', __METHOD__);

		// Etiqueta del nodo raíz
		$xml = '<' . $this->etiqueta;

		// Atributos del nodo raíz: 'error', definidos por el usuario y 'contenido'
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
	 *  método iJSON.
	 *
	 *  Convierte este objeto en una cadena que representa un objeto JavaScript
	 *   Este método proviene de implementar la interfaz iJSON.
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: cadena que representa el documento XML como objeto JavaScript.
	 *  Lanza excepción si: - no está cubierta la etiqueta
	 */
	public function toJSON () {

		if ( $this->etiqueta == '' )  throw new Excepcion('Es necesaria etiqueta', __METHOD__);

		// "Nodo raíz"
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

		// Cierre del "nodo raíz"
		$json .= '}';
		if ( $this->bNodoRaiz )  $json .= '}';

		return $json;

	}

}

?>