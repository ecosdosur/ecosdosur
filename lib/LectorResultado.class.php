<?php

/*
 * Clase LectorResultado.
 *
 *  Representa las operaciones que se pueden realizar con una consulta a base de datos, 
 *   leyéndola registro a registro.
 *
 *  Implementa la interfaz iJSON
 *
 *  Autor: Pentared
 *  Última actualización: 09/01/2007
 */
abstract class LectorResultado implements iJSON {

	// Identificador de la consulta
	protected $idConsulta;

	// Consulta realizada
	protected $sql;

	// Atributos de la consulta
	protected $numRegistros;
	protected $numColumnas;

	// Atributos internos
	protected $registroActual;
	protected $arrayNombreColumnas;
	protected $arrayTipoColumnas;

	// Conversor de tipos
	protected $conversor;

	/*
	 *  constructor de la clase
	 *
	 *  Inicializa el objeto
	 *  
	 *  Parámetros: $idConsulta: identificador de la consulta
	 *              $sql: consulta SQL realizada
	 */
	public function __construct ($idConsulta, $sql) {

		// Identificador de la consulta
		$this->idConsulta = $idConsulta;

		// Consulta realizada
		$this->sql = $sql;

		// Atributos básicos de la consulta: llamadas a métodos por instanciar
		$this->_getNumRegistros();
		$this->_getNumColumnas();

		// Registro actual (ninguno por ahora)
		$this->registroActual = false;

		// Nombres y tipos de columnas
		$this->_getNombresYTiposColumnas();

		// Conversor
		$this->conversor = null;

	}

	/*
	 *  desconstructor de la clase
	 *
	 *  Realiza operaciones de limpieza
	 */
	function __destruct () {

		$this->_liberarResultado();

	}

	/*
	 *  método getQuery.
	 *
	 *  Obtiene la consulta SQL que generó este lector
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: cadena que representa la consulta SQL
	 */
	public function getQuery () { return $this->sql; }

	/*
	 *  método getNumRegistros.
	 *
	 *  Obtiene el número de registros obtenidos por la consulta realizada
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: número de registros totales de la consulta
	 *  Lanza excepción si: - error en base de datos
	 */
	public function getNumRegistros () { return $this->numRegistros; }

	/*
	 *  método getNumColumnas.
	 *
	 *  Obtiene el número de columnas de la consulta realizada
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: número de columnas de la consulta
	 *  Lanza excepción si: - error en base de datos
	 */
	public function getNumColumnas () { return $this->numColumnas; }

	/*
	 *  método getRegistro.
	 *
	 *  Obtiene el registro en el que se encuentre el cursor como array clave-valor,
	 *   donde clave es el nombre de la columna.
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: un array con el registro seleccionado
	 *  Lanza excepción si: - error en base de datos
	 *                      - se ha sobrepasado el rango de la consulta
	 */
	public function getRegistro () {

		if ( $this->registroActual === false ) {
			throw new Excepcion('fuera de rango');
		}

		return $this->registroActual;

	}

	/*
	 *  método getColumnas.
	 *
	 *  Obtiene un array con los nombres y orden de las columnas devueltas.
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: un array con las columnas devueltas
	 *  Lanza excepción si: - error en base de datos
	 *                      - no se puede obtener
	 */
	public function getColumnas () {

		return $this->arrayNombreColumnas;

	}

	/*
	 *  método getValor.
	 *
	 *  Obtiene el valor en el registro actual que corresponde a la clave indicada.
	 *  
	 *  Parámetros: $clave: nombre de la columna cuyo valor se quiere recuperar, o bien su ordinal 
	 *                      (empezando por el cero).
	 *  Devuelve: valor seleccionado
	 *  Lanza excepción si: - error en base de datos
	 *                      - se ha sobrepasado el rango de la consulta
	 *                      - no existe ese campo
	 */
	public function getValor ($clave) {

		// Control de rango
		if ( $this->registroActual === false ) {
			throw new Excepcion('fuera de rango', __METHOD__);
		}
	
		// Control de clave existente
		if ( !array_key_exists($clave, $this->registroActual) ) {
			throw new Excepcion("No existe la columna '$clave'", __METHOD__);
		}

		return $this->registroActual[$clave];

	}

	/*
	 *  método setConversorTipos.
	 *
	 *  Asigna un conversor de tipos de datos, para que muestre cada dato
	 *   en el formato adecuado.
	 *  
	 *  Parámetros: $conversor: objeto que convierta datos según su tipo de datos.
	 */
	public function setConversorTipos ($conversor) {

		$this->conversor = $conversor;

	}

	/*
	 *  método toJSON.
	 *
	 *  Convierte a JSON la representación actual del objeto
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: una cadena que representa el objeto en JSON
	 *  Lanza excepción si: - error en la conversión
	 */
	public function toJSON () {

		if ( $this->registroActual === false ) {
			throw new Excepcion('fuera de rango', __METHOD__);
		}

		return JSONUtils::simpleArrayToJSON($this->registroActual);

	}

	// Métodos abstractos públicos
	/*
	 *  método siguiente.
	 *
	 *  Mueve el cursor al siguiente registro de la consulta.
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: 'true' si hay un nuevo registro o 'false' si hemos llegado al final
	 *  Lanza excepción si: - error en base de datos
	 */
	abstract public function siguiente ();

	/*
	 *  método situarEnRegistro.
	 *
	 *  Mueve el cursor al número de registro indicado (de 0 a numRegistros-1).
	 *  
	 *  Parámetros: $numRegistro: número de registro donde situarse
	 *  Devuelve: 'true' si existe el registro o 'false' si no lo hay
	 *  Lanza excepción si: - error en base de datos
	 */
	abstract public function situarEnRegistro ($numRegistro);

	// Métodos abstractos: específicos según el método de acceso a base de datos
	/*
	 *  método _getNumRegistros.
	 *
	 *  Vuelca el número de registros de la consulta en '$this->numRegistros'. 
	 *   Ha de ser instanciable por las clases que hereden de esta.
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - error en base de datos
	 */
	abstract protected function _getNumRegistros ();

	/*
	 *  método _getNumColumnas.
	 *
	 *  Vuelca el número de columnas de la consulta en '$this->numColumnas'. 
	 *   Ha de ser instanciable por las clases que hereden de esta.
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - error en base de datos
	 */
	abstract protected function _getNumColumnas ();

	/*
	 *  método _getNombresYTiposColumnas.
	 *
	 *  Rellena las variables '$this->arrayNombreColumnas' y '$this->arrayTipoColumnas'. 
	 *   Ha de ser instanciable por las clases que hereden de esta.
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - error en base de datos
	 */
	abstract protected function _getNombresYTiposColumnas ();

	/*
	 *  método _liberarResultado.
	 *
	 *  Realiza las operaciones para liberar el resultado de la consulta.
	 *   Ha de ser instanciable por las clases que hereden de esta.
	 *  
	 *  Parámetros: no tiene
	 */
	abstract protected function _liberarResultado ();

}

?>
