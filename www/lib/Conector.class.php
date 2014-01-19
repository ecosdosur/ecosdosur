<?php

/*
 * Clase Conector.
 *
 *  Representa una conexión genérica a base de datos, con sus operaciones básicas.
 *
 *  Autor: Pentared
 *  Última actualización: 29/01/2007
 */
abstract class Conector implements iFactoria {

	// Atributos de la conexión a base de datos
	protected $url;
	protected $puerto;
	protected $usuario;
	protected $password;

	// Atrubutos adicionales
	protected $libreria;

	// Identificador de la conexión a base de datos
	protected $idConexion;

	// Constructor por defecto
	//  Los constructors de las clases que hereden de ésta deben llamar previamente a éste
	public function __construct ($parametros) {

		$this->setUrl($parametros['url']);
		$this->setPuerto($parametros['puerto']);
		$this->setUsuario($parametros['usuario']);
		$this->setPassword($parametros['password']);
		$this->idConexion = 0;

	}

	// Destructor
	function __destruct () { 

		$this->_cierraConexion();

	}

	/*
	 *  método getInstancia.
	 *
	 *  Crea un objeto conector según el driver seleccionado.
	 *   Este método no puede ser sobrepuesto por las clases hijas
	 *  
	 *  Parámetros: $parametros: array con los parámetros de la conexión.
	 *                           Ha de tener, al menos, la clave 'driver' cubierta. Son opcionales
	 *                           las claves 'url', 'usuario' y 'password'
	 *  Devuelve: un objeto conector del driver que se ha determinado
	 *  Lanza excepción si: - no se encuentra el driver seleccionado.
	 */
	public static final function getInstancia ($parametros) {

		$driver = trim($parametros['driver']);
		$nombreClase = 'Conector' . $driver;

		if ( ($driver == '') || !autoCarga($nombreClase) ) {
			throw new Excepcion("Driver no encontrado ($driver)", __METHOD__);
		}

		//return new $nombreClase($parametros['url'], $parametros['usuario'], $parametros['password']);
		return new $nombreClase($parametros);

	}

	// Métodos abstractos: han de ser instanciados por las clases que herenden de ésta
	/*
	 *  método _getIdConexion.
	 *
	 *  Obtiene el identificador de la conexión a base de datos.
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: un identificador válido de la conexión a base de datos
	 *  Lanza excepción si: - error en base de datos
	 */
	abstract protected function _getIdConexion ();

	/*
	 *  método _cierraConexion.
	 *
	 *  Cierra la conexión abierta a la base de datos
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: nada 
	 *  Lanza excepción si: - en ningún caso
	 */
	abstract protected function _cierraConexion ();

	/*
	 *  método _consulta.
	 *
	 *  Realiza una consulta a la base de datos nativamente. Debe devolver un objeto que implemente
	 *   la interfaz ResultadoConsulta.
	 *  
	 *  Parámetros: $sql: consulta a base de datos
	 *  Devuelve: un objeto que implemente la interfaz ResultadoConsulta
	 *  Lanza excepción si: - error en base de datos
	 */
	abstract protected function _consulta ($sql);

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
	abstract protected function _ejecuta ($sql);

	// Métodos protegidos
	/*
	 *  método setConexion.
	 *
	 *  Realiza la conexión a base de datos (si ésta no está activa)
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: nada
	 *  Lanza excepción si: - algún parámetro crítico de la conexión no está disponible
	 *                      - error al conectar con base de datos
	 */
	protected function setConexion () {

		// Si ya está abierta, salimos
		if ( $this->idConexion > 0 )  return;

		// Control de parámetros
		if ( trim($this->url) == '' )  throw new Excepcion('url no válida', __METHOD__);
		//if ( trim($this->puerto) == 0 )  throw new Excepcion('puerto no válido', __METHOD__);
		//if ( trim($this->usuario) == '' )  throw new Excepcion($iniEx . 'usuario no válido', __METHOD__);

		// Llamada al método a instanciar en la clase que herede de ésta
		$this->idConexion = $this->_getIdConexion();

	}

	// Métodos instanciados
	// - Obtención de atributos
	public function setUrl ($url) { $this->url = $url; }
	public function setPuerto ($puerto) { $this->puerto = $puerto; }
	public function setUsuario ($usuario) { $this->usuario = $usuario; }
	public function setPassword ($password) { $this->password = $password; }
	public function setLibreria ($libreria) { $this->libreria = $libreria; }
	// - Acceso a atributos
	public function getIdConexion () { return $this->$idConexion; }
	public function getUrl () { return $this->url; }
	public function getPuerto () { return $this->puerto; }
	public function getUsuario () { return $this->usuario; }
	public function getPassword () { return $this->password; }
	public function getLibreria () { return $this->libreria; }

	// Métodos de operaciones básicas
	/*
	 *  método consulta.
	 *
	 *  Realiza una consulta a la base de datos.
	 *  
	 *  Parámetros: $sql: consulta a base de datos
	 *  Devuelve: un objeto que implemente la interfaz iResultadoConsulta
	 *  Lanza excepción si: - algún parámetro crítico de la conexión no está disponible
	 *                      - el parámetro no es una consulta SQL válida
	 *                      - error en base de datos
	 */
	public function consulta ($sql) {

		// Nos aseguramos de que exista una conexión
		$this->setConexion();

		$sql = trim($sql);

		// Control de parámetros de entrada
		if ( $sql == '' )  throw new Excepcion('consulta vacía', __METHOD__);
		if ( ((stripos($sql, 'UPDATE') !== false ) && (stripos($sql, 'UPDATE') == 0)) || 
			 ((stripos($sql, 'DELETE') !== false ) && (stripos($sql, 'DELETE') == 0)) || 
			 ((stripos($sql, 'INSERT') !== false ) && (stripos($sql, 'INSERT') == 0)) || 
			 ((stripos($sql, 'REPLACE') !== false ) && (stripos($sql, 'REPLACE')) == 0) ) {
			throw new Excepcion("consulta no válida ($sql)", __METHOD__);
		}
		// Llamada al método que realiza la consulta: debe ser instanciado por las clases que hereden de ésta
		return $this->_consulta($sql);

	}

	/*
	 *  método ejecuta.
	 *
	 *  Realiza una consulta a la base de datos que provoca cambios en alguna de sus tablas.
	 *  
	 *  Parámetros: $sql: consulta a base de datos
	 *  Devuelve: número de filas afectadas
	 *  Lanza excepción si: - algún parámetro crítico de la conexión no está disponible
	 *                      - el parámetro no es una consulta SQL válida
	 *                      - error en base de datos
	 */
	public function ejecuta ($sql) {

		// Nos aseguramos de que exista una conexión
		$this->setConexion();

		// Control de parámetros de entrada
		if ( trim($sql) == '' )  throw new Excepcion('consulta vacía', __METHOD__);
		if ( (stripos($sql, 'UPDATE') === false) && (stripos($sql, 'DELETE') === false) && 
			 (stripos($sql, 'INSERT') === false) && (stripos($sql, 'REPLACE') === false) ) {
			throw new Excepcion("consulta no válida ($sql)", __METHOD__);
		}
		// Llamada al método que realiza la consulta: debe ser instanciado por las clases que hereden de ésta
		return $this->_ejecuta($sql);

	}

}

?>
