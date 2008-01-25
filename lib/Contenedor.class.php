<?php

/*
 * Clase Contenedor.
 *
 *  Representa un almacén (contenedor) de variables. Encapsula las principales funcionalidades
 *   de los arrays de PHP
 *  Cualquier clase que necesite un número indeterminado de atributos puede heredar de ella.
 *
 *  Autor: Pentared
 *  Última actualización: 22/11/2006
 */
class Contenedor {

	// Atributos de la clase
	protected $arrayVariables;

	// Constructor
	public function __construct ($arrayVariables = false) { 
	
		// Inicialización del array interno de variables
		$this->arrayVariables = array();
		if ( $arrayVariables !== false )  $this->arrayVariables = $arrayVariables;

	}

	/*
	 *  método hayVariable.
	 *
	 *  Comprueba si existe una determinada variable en el intérprete.
	 *  
	 *  Parámetros: $variable: cadena con el nombre de la variable
	 *  Devuelve: 'true' si existe; 'false' en caso contrario
	 */
	public function hayVariable ($variable) {

		return array_key_exists($variable, $this->arrayVariables);

	}

	/*
	 *  método hayVariables.
	 *
	 *  Comprueba si hay variables definidas en el contenedor
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: 'true' si hay variables; 'false' en caso contrario
	 */
	public function hayVariables () {

		return (sizeof($this->arrayVariables) > 0);

	}

	/*
	 *  método addVariable.
	 *
	 *  Añade una nueva variable con su respectivo valor. Machaca el valor anterior
	 *   si ya existía.
	 *  
	 *  Parámetros: $variable: cadena con el nombre de la variable
	 *              $valor: cadena con el valor de la variable
	 *  Devuelve: 'true' si ya existía; 'false' en caso contrario
	 */
	public function addVariable ($variable, $valor) {

		$bExistia = $this->hayVariable($variable);
		$this->arrayVariables[$variable] = $valor;

		return $bExistia;

	}

	/*
	 *  método addVariable.
	 *
	 *  Añade un grupo de variables a las ya existentes.
	 *  
	 *  Parámetros: $arrayNuevasVariables: nuevas variables a añadir
	 */
	public function addVariables ($arrayNuevasVariables) {

		//$this->arrayVariables = array_merge($this->arrayVariables, $arrayNuevasVariables);
		foreach ( $arrayNuevasVariables as $clave=>$valor ) {
			$this->addVariable($clave, $valor);
		}

	}

	/*
	 *  método getVariable.
	 *
	 *  Devuelve el valor de una variable. En caso de que no existe, retorna 'false'.
	 *  
	 *  Parámetros: $variable: cadena con el nombre de la variable a obtener
	 */
	public function getVariable ($variable) {

		if ( !$this->hayVariable($variable) )  return false;
		else  return $this->arrayVariables[$variable];

	}

}

?>