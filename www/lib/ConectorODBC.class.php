<?php

/*
 * Clase ConectorODBC.
 *
 *  Representa una conexi�n a una base de datos por ODBC. Hereda de Conector.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 23/01/2007
 */
class ConectorODBC extends Conector {

	// Constructor
	public function __construct ($parametros) {

		// Llamada al constructor de la clase base
		parent::__construct($parametros);

	}

	/*
	 *  m�todo _query.
	 *
	 *  Realiza una consulta a la base de datos nativamente. Devuelve el identificador de la consulta.
	 *  
	 *  Par�metros: $sql: consulta a base de datos
	 *  Devuelve: identificador ODBC de la consulta
	 *  Lanza excepci�n si: - error en base de datos
	 */
	private function _query ($sql) {

		// Selecci�n de la librer�a: por ODBC no se selecciona la librer�a

		// Realizaci�n de la consulta
		$idConsulta = odbc_exec($this->idConexion, $sql);
		if ( $idConsulta === false ) {
			$numError = odbc_error();
			$error = odbc_errormsg();
			throw new Excepcion("no se pudo realizar consulta (Error $numError: $error)", __METHOD__);
		}

		// Valor devuelto
		return $idConsulta;

	}

	/*
	 *  m�todo _getIdConexion.
	 *
	 *  Obtiene el identificador de la conexi�n a base de datos.
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: un identificador v�lido de la conexi�n a base de datos
	 *  Lanza excepci�n si: - error en base de datos
	 */
	protected function _getIdConexion () {

		// Llamada nativa
		$idConexionODBC = odbc_connect($this->url, $this->usuario, $this->password, SQL_CUR_USE_ODBC);

		// Caso de error
		if ( $idConexionODBC == 0 ) {
			$numError = odbc_error();
			$error = odbc_errormsg();
			throw new Excepcion("no se pudo conectar (Error $numError: $error)", __METHOD__);
		}

		// Salida exitosa
		return $idConexionODBC;

	}

	/*
	 *  m�todo _cierraConexion.
	 *
	 *  Cierra la conexi�n abierta a la base de datos
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: nada 
	 *  Lanza excepci�n si: - en ning�n caso
	 */
	protected function _cierraConexion () {
	
		if ( $this->idConexion > 0 )  odbc_close($this->idConexion);

	}

	/*
	 *  m�todo _consulta.
	 *
	 *  Realiza una consulta a la base de datos nativamente. Devuelvo un objeto que implementa
	 *   la interfaz iLectorResultado, llamado LectorResultadoODBC.
	 *  
	 *  Par�metros: $sql: consulta a base de datos
	 *  Devuelve: un objeto que implemente la interfaz ResultadoConsulta
	 *  Lanza excepci�n si: - error en base de datos
	 */
	protected function _consulta ($sql) {

		// Se retorna un objeto que implementa la intefaz iResultadoConsulta
		return new LectorResultadoODBC($this->_query($sql), $sql);

	}

	/*
	 *  m�todo _ejecuta.
	 *
	 *  Realiza una consulta a la base de datos que provoca cambios en alguna de sus tablas nativamente. Debe 
	 *   devolver el n�mero de filas que se ven afectadas.
	 *
	 *  OJO: en modo ODBC no se sabe cu�ntas filas quedan afectadas
	 *  
	 *  Par�metros: $sql: consulta a base de datos
	 *  Devuelve: el n�mero de filas que se ven afectadas
	 *  Lanza excepci�n si: - error en base de datos
	 */
	protected function _ejecuta ($sql) {

		// Consulta a base de datos
		$idConsulta = $this->_query($sql);

		// Devuelve el n�mero de filas afectadas
		return odbc_num_rows($idConsulta);

	}

}

?>