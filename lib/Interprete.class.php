<?php

/*
 * Clase Interprete.
 *
 *  Contiene funcionalidad para interpretar cadenas 
 *   que no lo han sido ya por el motor de PHP.
 *  Hereda de Contenedor.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 23/11/2006
 */
class Interprete extends Contenedor {

	// Constructor
	public function __construct ($arrayVariables = false) { 

		// Llamada al constructor de la clase base
		parent::__construct($arrayVariables);

	}

	/*
	 *  m�todo interpreta.
	 *
	 *  Interpreta una cadena con las variables que se le pasan, o las que tiene dentro
	 *   del objeto, en su defecto. En el primer caso, puede ser llamado de forma est�tica
	 *  
	 *  Par�metros: $cadena: cadena que se interpretar�
	 *              $arrayVariables: array clave-valor con las variables para sustituir
	 *  Devuelve: cadena intepretada
	 *  Lanza excepci�n: - si se llama sin segundo par�metro de forma est�tica
	 */
	public function interpreta ($cadena, $arrayVariables = false) {

		if ( $arrayVariables === false ) {
			/*
			if ( !isset($this) && (get_class($this) == __CLASS__) ) {
				throw new Excepcion('Falta el segundo par�metro', __METHOD__);
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