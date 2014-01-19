<?php

/*
 * Clase LectorResultadoODBC.
 *
 *  Realiza las operaciones registro a registro sobre una consulta por ODBC. 
 *
 * Hereda de LectorResultado.
 *
 *  Autor: Pentared
 *  Última actualización: 27/11/2006
 */
class LectorResultadoODBC extends LectorResultado {

	// Atributos de la consulta
	private $numFila;

	/*
	 *  constructor de la clase
	 *
	 *  Inicializa el objeto
	 *  
	 *  Parámetros: $idConsulta: identificador de la consulta
	 *              $sql: consulta SQL realizada
	 */
	public function __construct ($idConsulta, $sql) {

		// Llamada al constructor de la clase base
		parent::__construct($idConsulta, $sql);

		// Atributos básicos a mayores de esta clase
		$this->numFila = 0;

	}

	/*
	 *  método siguiente.
	 *
	 *  Mueve el cursor al siguiente registro de la consulta.
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: 'true' si hay un nuevo registro o 'false' si hemos llegado al final
	 *  Lanza excepción si: - error en base de datos
	 */
	public function siguiente () {

		// Recogemos el registro de la base de datos
		$this->registroActual = odbc_fetch_array($this->idConsulta, ++$this->numFila);
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
	 *  método situarEnRegistro.
	 *
	 *  Mueve el cursor al número de registro indicado (de 0 a numRegistros-1).
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: 'true' si existe el registro o 'false' si no lo hay
	 *  Lanza excepción si: - error en base de datos
	 */
	public function situarEnRegistro ($numRegistro) {

		$this->numFila = $numRegistro;
		return ($numRegistro >= 0) && ($numRegistro < $this->numRegistros);
		//return mysql_data_seek($this->idConsulta, $numRegistro);

	}

	/*
	 *  método _getNumRegistros.
	 *
	 *  Obtiene el número de registros de la consulta.
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - error en base de datos
	 */
	protected function _getNumRegistros () {

		// El número de registros puede que se tenga que obtener de una forma alternativa
		$this->numRegistros = odbc_num_rows($this->idConsulta);
		if ( $this->numRegistros == -1 ) {
			$contador = 0;
			while ( odbc_fetch_array($this->idConsulta, ++$contador) ) { }
			$this->numRegistros = $contador - 1;
		}

	}

	/*
	 *  método _getNumColumnas.
	 *
	 *  Obtiene el número de columnas de la consulta.
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - error en base de datos
	 */
	protected function _getNumColumnas () {

		$this->numColumnas = odbc_num_fields($this->idConsulta);

	}

	/*
	 *  método _getNombresYTiposColumnas.
	 *
	 *  Rellena los arrays con los nombres y tipos de las columnas de la consulta.
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - error en base de datos
	 */
	protected function _getNombresYTiposColumnas () {

		// Nombres y tipos de columnas
		$this->arrayNombreColumnas = array();
		$this->arrayTiposColumnas = array();
		for ( $k = 1; $k <= $this->numColumnas; $k++ ) {
			$nombre = odbc_field_name($this->idConsulta, $k);
			if ( $nombre === false )  throw new Excepcion('no es posible obtener meta información', __METHOD__);
			$this->arrayNombreColumnas[] = $nombre;
			$this->arrayTiposColumnas[$nombre] = odbc_field_type($this->idConsulta, $k);
		}

	}

	/*
	 *  método _liberarResultado.
	 *
	 *  Realiza las operaciones para liberar el resultado de la consulta.
	 *  
	 *  Parámetros: no tiene
	 */
	protected function _liberarResultado () {

		odbc_free_result($this->idConsulta);

	}

}

?>
