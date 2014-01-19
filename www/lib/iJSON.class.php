<?php

/*
 * Interfaz iJSON
 *
 *  Representa las operaciones que se pueden realizar convirtiendo valores a JSON
 */
interface iJSON {

	/*
	 *  m�todo toJSON.
	 *
	 *  Convierte a JSON la representaci�n actual de un objeto
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: una cadena que representa el objeto en JSON
	 *  Lanza excepci�n si: - error en la conversi�n
	 */
	public function toJSON ();

}

?>
