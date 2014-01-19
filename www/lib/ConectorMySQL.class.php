<?php

/*
 * Clase ConectorMySQL.
 *
 *  Representa una conexión a una base de datos MySQL. Hereda de Conector.
 *
 *  Autor: Pentared
 *  Última actualización: 23/01/2007
 */
class ConectorMySQL extends Conector {

	// Puerto por defecto
	const PUERTO_DEFECTO = 3306;

	// Constructor
	public function __construct ($parametros) {

		// Llamada al constructor de la clase base
		parent::__construct($parametros);

		// Puerto de MySQL
		if ( $this->puerto == '' )  $this->setPuerto(self::PUERTO_DEFECTO);

	}

	/*
	 *  método _query.
	 *
	 *  Realiza una consulta a la base de datos nativamente. Devuelve el identificador de la consulta.
	 *  
	 *  Parámetros: $sql: consulta a base de datos
	 *  Devuelve: identificador MySQL de la consulta
	 *  Lanza excepción si: - error en base de datos
	 */
	private function _query ($sql) {

		// Selección de la librería
		if ( trim($this->libreria) != '' ) {
			$bResultado = mysql_select_db($this->libreria, $this->idConexion);
			if ( $bResultado === false ) {
				$numError = mysql_errno();
				$error = mysql_error();
				throw new Excepcion("no se pudo seleccionar la librería (Error $numError: $error)", __METHOD__);
			}
		}

		// Realización de la consulta
		$idConsulta = mysql_query($sql);
		if ( $idConsulta === false ) {
			$numError = mysql_errno();
			switch ( $numError ) {
				case 1062: { $error = 'No se puede crear el registro. La clave ya existe'; break; }
				case 1451: { $error = 'No se puede borrar el registro. Se está usando en otra tabla'; break; }
				default: { $error = "Error $numError: " . mysql_error() . ")"; break; }
			}
			throw new Excepcion("no se pudo realizar la consulta ($error).", __METHOD__);
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
		$rutaServidor = $this->url . ':' . $this->puerto;
		$idConexionMySQL = mysql_connect($rutaServidor, $this->usuario, $this->password);

		// Caso de error
		if ( $idConexionMySQL == 0 ) {
			$numError = mysql_errno();
			$error = mysql_error();
			throw new Excepcion("no se pudo conectar (Error $numError: $error)", __METHOD__);
		}

		// Salida exitosa
		return $idConexionMySQL;

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
	
		if ( $this->idConexion > 0 )  mysql_close($this->idConexion);

	}

	/*
	 *  método _consulta.
	 *
	 *  Realiza una consulta a la base de datos nativamente. Devuelvo un objeto que implementa
	 *   la interfaz iLectorResultado, llamado LectorResultadoMySQL.
	 *  
	 *  Parámetros: $sql: consulta a base de datos
	 *  Devuelve: un objeto que implemente la interfaz ResultadoConsulta
	 *  Lanza excepción si: - error en base de datos
	 */
	protected function _consulta ($sql) {

		// Se retorna un objeto que implementa la intefaz iResultadoConsulta
		return new LectorResultadoMySQL($this->_query($sql), $sql);

	}

	/*
	 *  método _ejecuta.
	 *
	 *  Realiza una consulta a la base de datos que provoca cambios en alguna de sus tablas nativamente. Debe 
	 *   devolver el número de filas que se ven afectadas.
	 *  
	 *  Parámetros: $sql: consulta a base de datos
	 *  Devuelve: el número de filas que se ven afectadas
	 *  Lanza excepción si: - error en base de datos
	 */
	protected function _ejecuta ($sql) {

		// Consulta a base de datos
		$this->_query($sql);

		// Devuelve el número de filas afectadas
		return mysql_affected_rows($this->idConexion);

	}

}

?>