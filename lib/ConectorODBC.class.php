<?php

/*
 * Clase ConectorODBC.
 *
 *  Representa una conexión a una base de datos por ODBC. Hereda de Conector.
 *
 *  Autor: Pentared
 *  Última actualización: 23/01/2007
 */
class ConectorODBC extends Conector {

	// Constructor
	public function __construct ($parametros) {

		// Llamada al constructor de la clase base
		parent::__construct($parametros);

	}

	/*
	 *  método _query.
	 *
	 *  Realiza una consulta a la base de datos nativamente. Devuelve el identificador de la consulta.
	 *  
	 *  Parámetros: $sql: consulta a base de datos
	 *  Devuelve: identificador ODBC de la consulta
	 *  Lanza excepción si: - error en base de datos
	 */
	private function _query ($sql) {

		// Selección de la librería: por ODBC no se selecciona la librería

		// Realización de la consulta
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
	 *  método _getIdConexion.
	 *
	 *  Obtiene el identificador de la conexión a base de datos.
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: un identificador válido de la conexión a base de datos
	 *  Lanza excepción si: - error en base de datos
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
	 *  método _cierraConexion.
	 *
	 *  Cierra la conexión abierta a la base de datos
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: nada 
	 *  Lanza excepción si: - en ningún caso
	 */
	protected function _cierraConexion () {
	
		if ( $this->idConexion > 0 )  odbc_close($this->idConexion);

	}

	/*
	 *  método _consulta.
	 *
	 *  Realiza una consulta a la base de datos nativamente. Devuelvo un objeto que implementa
	 *   la interfaz iLectorResultado, llamado LectorResultadoODBC.
	 *  
	 *  Parámetros: $sql: consulta a base de datos
	 *  Devuelve: un objeto que implemente la interfaz ResultadoConsulta
	 *  Lanza excepción si: - error en base de datos
	 */
	protected function _consulta ($sql) {

		// Se retorna un objeto que implementa la intefaz iResultadoConsulta
		return new LectorResultadoODBC($this->_query($sql), $sql);

	}

	/*
	 *  método _ejecuta.
	 *
	 *  Realiza una consulta a la base de datos que provoca cambios en alguna de sus tablas nativamente. Debe 
	 *   devolver el número de filas que se ven afectadas.
	 *
	 *  OJO: en modo ODBC no se sabe cuántas filas quedan afectadas
	 *  
	 *  Parámetros: $sql: consulta a base de datos
	 *  Devuelve: el número de filas que se ven afectadas
	 *  Lanza excepción si: - error en base de datos
	 */
	protected function _ejecuta ($sql) {

		// Consulta a base de datos
		$idConsulta = $this->_query($sql);

		// Devuelve el número de filas afectadas
		return odbc_num_rows($idConsulta);

	}

}

?>