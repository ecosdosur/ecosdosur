<?php

/*
 * Clase Contenedor.
 *
 *  Representa un almac�n (contenedor) de variables. Encapsula las principales funcionalidades
 *   de los arrays de PHP
 *  Cualquier clase que necesite un n�mero indeterminado de atributos puede heredar de ella.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 22/11/2006
 */
class Contenedor {

	// Atributos de la clase
	protected $arrayVariables;

	// Constructor
	public function __construct ($arrayVariables = false) { 
	
		// Inicializaci�n del array interno de variables
		$this->arrayVariables = array();
		if ( $arrayVariables !== false )  $this->arrayVariables = $arrayVariables;

	}

	/*
	 *  m�todo hayVariable.
	 *
	 *  Comprueba si existe una determinada variable en el int�rprete.
	 *  
	 *  Par�metros: $variable: cadena con el nombre de la variable
	 *  Devuelve: 'true' si existe; 'false' en caso contrario
	 */
	public function hayVariable ($variable) {

		return array_key_exists($variable, $this->arrayVariables);

	}

	/*
	 *  m�todo hayVariables.
	 *
	 *  Comprueba si hay variables definidas en el contenedor
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: 'true' si hay variables; 'false' en caso contrario
	 */
	public function hayVariables () {

		return (sizeof($this->arrayVariables) > 0);

	}

	/*
	 *  m�todo addVariable.
	 *
	 *  A�ade una nueva variable con su respectivo valor. Machaca el valor anterior
	 *   si ya exist�a.
	 *  
	 *  Par�metros: $variable: cadena con el nombre de la variable
	 *              $valor: cadena con el valor de la variable
	 *  Devuelve: 'true' si ya exist�a; 'false' en caso contrario
	 */
	public function addVariable ($variable, $valor) {

		$bExistia = $this->hayVariable($variable);
		$this->arrayVariables[$variable] = $valor;

		return $bExistia;

	}

	/*
	 *  m�todo addVariable.
	 *
	 *  A�ade un grupo de variables a las ya existentes.
	 *  
	 *  Par�metros: $arrayNuevasVariables: nuevas variables a a�adir
	 */
	public function addVariables ($arrayNuevasVariables) {

		//$this->arrayVariables = array_merge($this->arrayVariables, $arrayNuevasVariables);
		foreach ( $arrayNuevasVariables as $clave=>$valor ) {
			$this->addVariable($clave, $valor);
		}

	}

	/*
	 *  m�todo getVariable.
	 *
	 *  Devuelve el valor de una variable. En caso de que no existe, retorna 'false'.
	 *  
	 *  Par�metros: $variable: cadena con el nombre de la variable a obtener
	 */
	public function getVariable ($variable) {

		if ( !$this->hayVariable($variable) )  return false;
		else  return $this->arrayVariables[$variable];

	}

}

?>