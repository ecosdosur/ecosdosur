<?php

/*
 * Interfaz iJSON
 *
 *  Representa las operaciones que se pueden realizar convirtiendo valores a JSON
 */
interface iJSON {

	/*
	 *  método toJSON.
	 *
	 *  Convierte a JSON la representación actual de un objeto
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: una cadena que representa el objeto en JSON
	 *  Lanza excepción si: - error en la conversión
	 */
	public function toJSON ();

}

?>
