<?php

/*
 * Clase Conector.
 *
 *  Representa una conexi�n gen�rica a base de datos, con sus operaciones b�sicas.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 29/01/2007
 */
abstract class Conector implements iFactoria {

	// Atributos de la conexi�n a base de datos
	protected $url;
	protected $puerto;
	protected $usuario;
	protected $password;

	// Atrubutos adicionales
	protected $libreria;

	// Identificador de la conexi�n a base de datos
	protected $idConexion;

	// Constructor por defecto
	//  Los constructors de las clases que hereden de �sta deben llamar previamente a �ste
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
	 *  m�todo getInstancia.
	 *
	 *  Crea un objeto conector seg�n el driver seleccionado.
	 *   Este m�todo no puede ser sobrepuesto por las clases hijas
	 *  
	 *  Par�metros: $parametros: array con los par�metros de la conexi�n.
	 *                           Ha de tener, al menos, la clave 'driver' cubierta. Son opcionales
	 *                           las claves 'url', 'usuario' y 'password'
	 *  Devuelve: un objeto conector del driver que se ha determinado
	 *  Lanza excepci�n si: - no se encuentra el driver seleccionado.
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

	// M�todos abstractos: han de ser instanciados por las clases que herenden de �sta
	/*
	 *  m�todo _getIdConexion.
	 *
	 *  Obtiene el identificador de la conexi�n a base de datos.
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: un identificador v�lido de la conexi�n a base de datos
	 *  Lanza excepci�n si: - error en base de datos
	 */
	abstract protected function _getIdConexion ();

	/*
	 *  m�todo _cierraConexion.
	 *
	 *  Cierra la conexi�n abierta a la base de datos
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: nada 
	 *  Lanza excepci�n si: - en ning�n caso
	 */
	abstract protected function _cierraConexion ();

	/*
	 *  m�todo _consulta.
	 *
	 *  Realiza una consulta a la base de datos nativamente. Debe devolver un objeto que implemente
	 *   la interfaz ResultadoConsulta.
	 *  
	 *  Par�metros: $sql: consulta a base de datos
	 *  Devuelve: un objeto que implemente la interfaz ResultadoConsulta
	 *  Lanza excepci�n si: - error en base de datos
	 */
	abstract protected function _consulta ($sql);

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
	abstract protected function _ejecuta ($sql);

	// M�todos protegidos
	/*
	 *  m�todo setConexion.
	 *
	 *  Realiza la conexi�n a base de datos (si �sta no est� activa)
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: nada
	 *  Lanza excepci�n si: - alg�n par�metro cr�tico de la conexi�n no est� disponible
	 *                      - error al conectar con base de datos
	 */
	protected function setConexion () {

		// Si ya est� abierta, salimos
		if ( $this->idConexion > 0 )  return;

		// Control de par�metros
		if ( trim($this->url) == '' )  throw new Excepcion('url no v�lida', __METHOD__);
		//if ( trim($this->puerto) == 0 )  throw new Excepcion('puerto no v�lido', __METHOD__);
		//if ( trim($this->usuario) == '' )  throw new Excepcion($iniEx . 'usuario no v�lido', __METHOD__);

		// Llamada al m�todo a instanciar en la clase que herede de �sta
		$this->idConexion = $this->_getIdConexion();

	}

	// M�todos instanciados
	// - Obtenci�n de atributos
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

	// M�todos de operaciones b�sicas
	/*
	 *  m�todo consulta.
	 *
	 *  Realiza una consulta a la base de datos.
	 *  
	 *  Par�metros: $sql: consulta a base de datos
	 *  Devuelve: un objeto que implemente la interfaz iResultadoConsulta
	 *  Lanza excepci�n si: - alg�n par�metro cr�tico de la conexi�n no est� disponible
	 *                      - el par�metro no es una consulta SQL v�lida
	 *                      - error en base de datos
	 */
	public function consulta ($sql) {

		// Nos aseguramos de que exista una conexi�n
		$this->setConexion();

		$sql = trim($sql);

		// Control de par�metros de entrada
		if ( $sql == '' )  throw new Excepcion('consulta vac�a', __METHOD__);
		if ( ((stripos($sql, 'UPDATE') !== false ) && (stripos($sql, 'UPDATE') == 0)) || 
			 ((stripos($sql, 'DELETE') !== false ) && (stripos($sql, 'DELETE') == 0)) || 
			 ((stripos($sql, 'INSERT') !== false ) && (stripos($sql, 'INSERT') == 0)) || 
			 ((stripos($sql, 'REPLACE') !== false ) && (stripos($sql, 'REPLACE')) == 0) ) {
			throw new Excepcion("consulta no v�lida ($sql)", __METHOD__);
		}
		// Llamada al m�todo que realiza la consulta: debe ser instanciado por las clases que hereden de �sta
		return $this->_consulta($sql);

	}

	/*
	 *  m�todo ejecuta.
	 *
	 *  Realiza una consulta a la base de datos que provoca cambios en alguna de sus tablas.
	 *  
	 *  Par�metros: $sql: consulta a base de datos
	 *  Devuelve: n�mero de filas afectadas
	 *  Lanza excepci�n si: - alg�n par�metro cr�tico de la conexi�n no est� disponible
	 *                      - el par�metro no es una consulta SQL v�lida
	 *                      - error en base de datos
	 */
	public function ejecuta ($sql) {

		// Nos aseguramos de que exista una conexi�n
		$this->setConexion();

		// Control de par�metros de entrada
		if ( trim($sql) == '' )  throw new Excepcion('consulta vac�a', __METHOD__);
		if ( (stripos($sql, 'UPDATE') === false) && (stripos($sql, 'DELETE') === false) && 
			 (stripos($sql, 'INSERT') === false) && (stripos($sql, 'REPLACE') === false) ) {
			throw new Excepcion("consulta no v�lida ($sql)", __METHOD__);
		}
		// Llamada al m�todo que realiza la consulta: debe ser instanciado por las clases que hereden de �sta
		return $this->_ejecuta($sql);

	}

}

?>
