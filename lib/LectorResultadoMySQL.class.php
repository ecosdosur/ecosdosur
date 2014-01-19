<?php

/*
 * Clase LectorResultadoMySQL.
 *
 *  Realiza las operaciones registro a registro sobre una consulta en MySQL. 
 *
 *  Hereda de LectorResultado
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 27/11/2006
 */
class LectorResultadoMySQL extends LectorResultado {

	/*
	 *  constructor de la clase
	 *
	 *  Inicializa el objeto
	 *  
	 *  Par�metros: $idConsulta: identificador de la consulta
	 *              $sql: consulta SQL realizada
	 */
	public function __construct ($idConsulta, $sql) {

		// Llamada al constructor de la clase base
		parent::__construct($idConsulta, $sql);

	}

	/*
	 *  m�todo siguiente.
	 *
	 *  Mueve el cursor al siguiente registro de la consulta.
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: 'true' si hay un nuevo registro o 'false' si hemos llegado al final
	 *  Lanza excepci�n si: - error en base de datos
	 */
	public function siguiente () {

		// Recogemos el registro de la base de datos
		$this->registroActual = mysql_fetch_assoc($this->idConsulta);
		if ( $this->registroActual === false )  return false;

		// Formateamos los resultados (si procede)
		if ( !is_null($this->conversor) ) {
			foreach ( $this->registroActual as $clave=>$valor ) {
				$valor = $this->conversor->convierte($valor, $this->arrayTiposColumnas[$clave]);
				$this->registroActual[$clave] = $valor;
			}
		}

		return true;

	}

	/*
	 *  m�todo situarEnRegistro.
	 *
	 *  Mueve el cursor al n�mero de registro indicado (de 0 a numRegistros-1).
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: 'true' si existe el registro o 'false' si no lo hay
	 *  Lanza excepci�n si: - error en base de datos
	 */
	public function situarEnRegistro ($numRegistro) {

		return mysql_data_seek($this->idConsulta, $numRegistro);

	}


	/*
	 *  m�todo _getNumRegistros.
	 *
	 *  Obtiene el n�mero de registros de la consulta.
	 *  
	 *  Par�metros: no tiene
	 *  Lanza excepci�n si: - error en base de datos
	 */
	protected function _getNumRegistros () {

		$this->numRegistros = mysql_num_rows($this->idConsulta);

	}

	/*
	 *  m�todo _getNumColumnas.
	 *
	 *  Obtiene el n�mero de columnas de la consulta.
	 *  
	 *  Par�metros: no tiene
	 *  Lanza excepci�n si: - error en base de datos
	 */
	protected function _getNumColumnas () {

		$this->numColumnas = mysql_num_fields($this->idConsulta);

	}

	/*
	 *  m�todo _getNombresYTiposColumnas.
	 *
	 *  Rellena los arrays con los nombres y tipos de las columnas de la consulta.
	 *  
	 *  Par�metros: no tiene
	 *  Lanza excepci�n si: - error en base de datos
	 */
	protected function _getNombresYTiposColumnas () {

		// Nombres y tipos de columnas
		$this->arrayNombreColumnas = array();
		$this->arrayTiposColumnas = array();
		for ( $k = 0; $k < $this->numColumnas; $k++ ) {
			$metaDatos = mysql_fetch_field($this->idConsulta, $k);
			if ( $metaDatos === false )  throw new Excepcion('no es posible obtener meta informaci�n', __METHOD__);
			$this->arrayNombreColumnas[] = $metaDatos->name;
			$this->arrayTiposColumnas[$metaDatos->name] = $metaDatos->type;
		}

	}

	/*
	 *  m�todo _liberarResultado.
	 *
	 *  Realiza las operaciones para liberar el resultado de la consulta.
	 *  
	 *  Par�metros: no tiene
	 */
	protected function _liberarResultado () {

		mysql_free_result($this->idConsulta);

	}

}

?>
