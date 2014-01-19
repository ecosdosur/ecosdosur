<?php

/*
 * Clase LectorResultado.
 *
 *  Representa las operaciones que se pueden realizar con una consulta a base de datos, 
 *   ley�ndola registro a registro.
 *
 *  Implementa la interfaz iJSON
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 09/01/2007
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
	 *  Par�metros: $idConsulta: identificador de la consulta
	 *              $sql: consulta SQL realizada
	 */
	public function __construct ($idConsulta, $sql) {

		// Identificador de la consulta
		$this->idConsulta = $idConsulta;

		// Consulta realizada
		$this->sql = $sql;

		// Atributos b�sicos de la consulta: llamadas a m�todos por instanciar
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
	 *  m�todo getQuery.
	 *
	 *  Obtiene la consulta SQL que gener� este lector
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: cadena que representa la consulta SQL
	 */
	public function getQuery () { return $this->sql; }

	/*
	 *  m�todo getNumRegistros.
	 *
	 *  Obtiene el n�mero de registros obtenidos por la consulta realizada
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: n�mero de registros totales de la consulta
	 *  Lanza excepci�n si: - error en base de datos
	 */
	public function getNumRegistros () { return $this->numRegistros; }

	/*
	 *  m�todo getNumColumnas.
	 *
	 *  Obtiene el n�mero de columnas de la consulta realizada
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: n�mero de columnas de la consulta
	 *  Lanza excepci�n si: - error en base de datos
	 */
	public function getNumColumnas () { return $this->numColumnas; }

	/*
	 *  m�todo getRegistro.
	 *
	 *  Obtiene el registro en el que se encuentre el cursor como array clave-valor,
	 *   donde clave es el nombre de la columna.
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: un array con el registro seleccionado
	 *  Lanza excepci�n si: - error en base de datos
	 *                      - se ha sobrepasado el rango de la consulta
	 */
	public function getRegistro () {

		if ( $this->registroActual === false ) {
			throw new Excepcion('fuera de rango');
		}

		return $this->registroActual;

	}

	/*
	 *  m�todo getColumnas.
	 *
	 *  Obtiene un array con los nombres y orden de las columnas devueltas.
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: un array con las columnas devueltas
	 *  Lanza excepci�n si: - error en base de datos
	 *                      - no se puede obtener
	 */
	public function getColumnas () {

		return $this->arrayNombreColumnas;

	}

	/*
	 *  m�todo getValor.
	 *
	 *  Obtiene el valor en el registro actual que corresponde a la clave indicada.
	 *  
	 *  Par�metros: $clave: nombre de la columna cuyo valor se quiere recuperar, o bien su ordinal 
	 *                      (empezando por el cero).
	 *  Devuelve: valor seleccionado
	 *  Lanza excepci�n si: - error en base de datos
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
	 *  m�todo setConversorTipos.
	 *
	 *  Asigna un conversor de tipos de datos, para que muestre cada dato
	 *   en el formato adecuado.
	 *  
	 *  Par�metros: $conversor: objeto que convierta datos seg�n su tipo de datos.
	 */
	public function setConversorTipos ($conversor) {

		$this->conversor = $conversor;

	}

	/*
	 *  m�todo toJSON.
	 *
	 *  Convierte a JSON la representaci�n actual del objeto
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: una cadena que representa el objeto en JSON
	 *  Lanza excepci�n si: - error en la conversi�n
	 */
	public function toJSON () {

		if ( $this->registroActual === false ) {
			throw new Excepcion('fuera de rango', __METHOD__);
		}

		return JSONUtils::simpleArrayToJSON($this->registroActual);

	}

	// M�todos abstractos p�blicos
	/*
	 *  m�todo siguiente.
	 *
	 *  Mueve el cursor al siguiente registro de la consulta.
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: 'true' si hay un nuevo registro o 'false' si hemos llegado al final
	 *  Lanza excepci�n si: - error en base de datos
	 */
	abstract public function siguiente ();

	/*
	 *  m�todo situarEnRegistro.
	 *
	 *  Mueve el cursor al n�mero de registro indicado (de 0 a numRegistros-1).
	 *  
	 *  Par�metros: $numRegistro: n�mero de registro donde situarse
	 *  Devuelve: 'true' si existe el registro o 'false' si no lo hay
	 *  Lanza excepci�n si: - error en base de datos
	 */
	abstract public function situarEnRegistro ($numRegistro);

	// M�todos abstractos: espec�ficos seg�n el m�todo de acceso a base de datos
	/*
	 *  m�todo _getNumRegistros.
	 *
	 *  Vuelca el n�mero de registros de la consulta en '$this->numRegistros'. 
	 *   Ha de ser instanciable por las clases que hereden de esta.
	 *  
	 *  Par�metros: no tiene
	 *  Lanza excepci�n si: - error en base de datos
	 */
	abstract protected function _getNumRegistros ();

	/*
	 *  m�todo _getNumColumnas.
	 *
	 *  Vuelca el n�mero de columnas de la consulta en '$this->numColumnas'. 
	 *   Ha de ser instanciable por las clases que hereden de esta.
	 *  
	 *  Par�metros: no tiene
	 *  Lanza excepci�n si: - error en base de datos
	 */
	abstract protected function _getNumColumnas ();

	/*
	 *  m�todo _getNombresYTiposColumnas.
	 *
	 *  Rellena las variables '$this->arrayNombreColumnas' y '$this->arrayTipoColumnas'. 
	 *   Ha de ser instanciable por las clases que hereden de esta.
	 *  
	 *  Par�metros: no tiene
	 *  Lanza excepci�n si: - error en base de datos
	 */
	abstract protected function _getNombresYTiposColumnas ();

	/*
	 *  m�todo _liberarResultado.
	 *
	 *  Realiza las operaciones para liberar el resultado de la consulta.
	 *   Ha de ser instanciable por las clases que hereden de esta.
	 *  
	 *  Par�metros: no tiene
	 */
	abstract protected function _liberarResultado ();

}

?>
