<?php

/*
 * Clase ConectorMySQL.
 *
 *  Representa una conexi�n a una base de datos MySQL. Hereda de Conector.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 23/01/2007
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
	 *  m�todo _query.
	 *
	 *  Realiza una consulta a la base de datos nativamente. Devuelve el identificador de la consulta.
	 *  
	 *  Par�metros: $sql: consulta a base de datos
	 *  Devuelve: identificador MySQL de la consulta
	 *  Lanza excepci�n si: - error en base de datos
	 */
	private function _query ($sql) {

		// Selecci�n de la librer�a
		if ( trim($this->libreria) != '' ) {
			$bResultado = mysql_select_db($this->libreria, $this->idConexion);
			if ( $bResultado === false ) {
				$numError = mysql_errno();
				$error = mysql_error();
				throw new Excepcion("no se pudo seleccionar la librer�a (Error $numError: $error)", __METHOD__);
			}
		}

		// Realizaci�n de la consulta
		$idConsulta = mysql_query($sql);
		if ( $idConsulta === false ) {
			$numError = mysql_errno();
			switch ( $numError ) {
				case 1062: { $error = 'No se puede crear el registro. La clave ya existe'; break; }
				case 1451: { $error = 'No se puede borrar el registro. Se est� usando en otra tabla'; break; }
				default: { $error = "Error $numError: " . mysql_error() . ")"; break; }
			}
			throw new Excepcion("no se pudo realizar la consulta ($error).", __METHOD__);
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
	 *  m�todo _cierraConexion.
	 *
	 *  Cierra la conexi�n abierta a la base de datos
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: nada 
	 *  Lanza excepci�n si: - en ning�n caso
	 */
	protected function _cierraConexion () {
	
		if ( $this->idConexion > 0 )  mysql_close($this->idConexion);

	}

	/*
	 *  m�todo _consulta.
	 *
	 *  Realiza una consulta a la base de datos nativamente. Devuelvo un objeto que implementa
	 *   la interfaz iLectorResultado, llamado LectorResultadoMySQL.
	 *  
	 *  Par�metros: $sql: consulta a base de datos
	 *  Devuelve: un objeto que implemente la interfaz ResultadoConsulta
	 *  Lanza excepci�n si: - error en base de datos
	 */
	protected function _consulta ($sql) {

		// Se retorna un objeto que implementa la intefaz iResultadoConsulta
		return new LectorResultadoMySQL($this->_query($sql), $sql);

	}

	/*
	 *  m�todo _ejecuta.
	 *
	 *  Realiza una consulta a la base de datos que provoca cambios en alguna de sus tablas nativamente. Debe 
	 *   devolver el n�mero de filas que se ven afectadas.
	 *  
	 *  Par�metros: $sql: consulta a base de datos
	 *  Devuelve: el n�mero de filas que se ven afectadas
	 *  Lanza excepci�n si: - error en base de datos
	 */
	protected function _ejecuta ($sql) {

		// Consulta a base de datos
		$this->_query($sql);

		// Devuelve el n�mero de filas afectadas
		return mysql_affected_rows($this->idConexion);

	}

}

?>