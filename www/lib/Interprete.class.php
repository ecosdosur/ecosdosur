<?php

/*
 * Clase Interprete.
 *
 *  Contiene funcionalidad para interpretar cadenas 
 *   que no lo han sido ya por el motor de PHP.
 *  Hereda de Contenedor.
 *
 *  Autor: Pentared
 *  Última actualización: 23/11/2006
 */
class Interprete extends Contenedor {

	// Constructor
	public function __construct ($arrayVariables = false) { 

		// Llamada al constructor de la clase base
		parent::__construct($arrayVariables);

	}

	/*
	 *  método interpreta.
	 *
	 *  Interpreta una cadena con las variables que se le pasan, o las que tiene dentro
	 *   del objeto, en su defecto. En el primer caso, puede ser llamado de forma estática
	 *  
	 *  Parámetros: $cadena: cadena que se interpretará
	 *              $arrayVariables: array clave-valor con las variables para sustituir
	 *  Devuelve: cadena intepretada
	 *  Lanza excepción: - si se llama sin segundo parámetro de forma estática
	 */
	public function interpreta ($cadena, $arrayVariables = false) {

		if ( $arrayVariables === false ) {
			/*
			if ( !isset($this) && (get_class($this) == __CLASS__) ) {
				throw new Excepcion('Falta el segundo parámetro', __METHOD__);
			}
			else */ $arrayVariables = &$this->arrayVariables;
		}

		// "Creamos" las variables localmente
		foreach ( $arrayVariables as $variable=>$valor ) {
			$$variable = $valor;
		}

		// Para realizar el eval(), la cadena debe tener las comillas dobles escapadas
		$cadena = str_replace('"', '\"', $cadena);
		$cadenaInterpretada = '';
		eval("\$cadenaInterpretada = \"$cadena\";");

		return $cadenaInterpretada;

	}

}

?>