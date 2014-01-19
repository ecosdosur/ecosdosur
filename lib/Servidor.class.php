<?php

/*
 * Clase Servidor.
 *
 *  Representa un servidor gen�rico que genera una respuesta en formato XML.
 *   Es una clase abstracta. Hereda de Interprete e implementa iXML e iFactoria.
 *
 *  IMPORTANTE: el m�dulo que incluye un objeto de una clase que herede a esta (en principio, 'servidor.php') ha de tener
 *    una serie de variables globales definidas para su correcto funcionamiento:
 *    - $_db (*): array con las diferentes conexiones del sistema y los par�metros de cada una de ellas, y
 *    - $_cnx (*): nombre de la conexi�n por defecto.
 *   Asimismo, para el �ptimo funcionamiento del log, es muy recomendable que existan las siguientes variables:
 *    - $_libreriaSistema: nombre de la librer�a donde se encuentra la tabla de usuarios del sistema, y
 *    - $_consultaUsuario: consulta parametrizada para obtener el usuario en claro del sistema
 *   (*) Las dos primeras variables se usan en el m�todo getConector() de la clase base
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 29/01/2007
 */
abstract class Servidor extends ItemConectable implements iXML, iFactoria {

	// Atributos
	protected $xmlResultado;
	protected $objLog;

	// Constructor: inicializa variables
	public function __construct () { 

		parent::__construct();

		$this->xmlResultado = null;
		$this->objLog = null;

	}

	/*
	 *  m�todo getInstancia.
	 *
	 *  Crea un objeto servidor seg�n el tipo de servidor seleccionado.
	 *   Este m�todo no puede ser sobrepuesto por las clases hijas
	 *  
	 *  Par�metros: $tipoServidor: nombre del servidor.
	 *  Devuelve: un objeto servidor del tipo seleccionado.
	 *  Lanza excepci�n si: - no se encuentra el tipo de servidor seleccionado.
	 */
	public static final function getInstancia ($tipoServidor) {

		$tipoServidor = trim($tipoServidor);
		$nombreClase = 'Servidor' . $tipoServidor;

		//if ( ($tipoServidor == '') || !include_once($nombreClase . '.class.php') ) {
		if ( ($tipoServidor == '') || !autoCarga($nombreClase) ) {
			throw new Excepcion('Servidor no encontrado', __METHOD__);
		}

		return new $nombreClase();

	}

	/*
	 *  m�todo setLog.
	 *
	 *  Asigna el objeto de la clase 'ManejadorLog' para realizar log.
	 *  
	 *  Par�metros: $log: objeto de la clase 'ManejadorLog'
	 */
	public function setLog ($objLog) {

		$this->objLog = $objLog;

	}

	/*
	 *  m�todo notifica.
	 *
	 *  Utiliza el log para realizar una notificaci�n.
	 *  
	 *  Par�metros: $operacion: operaci�n que se realiza
	 *              $usuario: usuario que realiza la operaci�n
	 *              $tabla: tabla sobre la que se realiza la operaci�n
	 *  Lanza excepci�n si: - no se puede abrir el fichero de log
	 *                      - error al recuperar el usuario
	 */
	protected function notifica ($operacion, $usuario, $tabla = '') {

		// S�lo se usa el log si hay fichero de log
		if ( !is_null($this->objLog) ) {
			// Se recupera el usuario sin encriptar si est� definida la consulta
			$this->objLog->notifica($operacion, $this->getUsuarioEnClaro($usuario), $tabla);
		}

	}

	/*
	 *  m�todo getUsuarioEnClaro.
	 *
	 *  Obtiene el c�digo de usuario en claro a partir del encriptado.
	 *  
	 *  Par�metros: $operacion: operaci�n que se realiza
	 *              $usuario: usuario que realiza la operaci�n
	 *              $tabla: tabla sobre la que se realiza la operaci�n
	 *  Devuelve: el c�digo del usuario en claro, o el usuario original si no se ha podido
	 *            obtener el usuario en claro.
	 */
	protected function getUsuarioEnClaro ($usuario) {

		global $_libreriaSistema;
		global $_consultaUsuario;
	
		try {
			// Si no se han definido la librer�a o la consulta, devolvemos el mismo usuario
			if ( !isset($_libreriaSistema) || !isset($_consultaUsuario) )  throw new Exception();
			// Pedimos el usuario
			$interprete = new Interprete();
			$interprete->addVariable('_libreriaSistema', $_libreriaSistema);
			$interprete->addVariable('usuarioCodificado', $usuario);
			$sql = $interprete->interpreta($_consultaUsuario);
			$conector = $this->getConector();
			$lector = $conector->consulta($sql);
			// Si no hay registros, devolvemos el mismo usuario
			if ( $lector->getNumRegistros() == 0 )  throw new Exception();
			$lector->siguiente();
			$usuario = $lector->getValor('USUARIO');
		}
		catch (Exception $ex) {
			// Devolvemos el mismo usuario
			// return $usuario;
		}

		return $usuario;

	}

	/*
	 *  m�todo ejecutaAcciones.
	 *
	 *  M�todo que se llama desde fuera para que se realice la ejecutoria del servidor.
	 *   Realiza las operaciones comunes.
	 *  
	 *  Par�metros: no tiene
	 *  Lanza excepci�n si: - no hay variables definidas en el servidor
	 *                      - se produce alg�n error realizando las acciones
	 */
	public function ejecutaAcciones () {

		// Operaciones comunes
		if ( !$this->hayVariables() )  throw new Excepcion('no hay variables', __METHOD__);

		// Llamada al m�todo que han de instanciar las clases que hereden de ella
		$this->_ejecutaAcciones();

	}

	/*
	 *  m�todo ejecutaAcciones.
	 *
	 *  Realiza la ejecutoria del servidor. 
	 *   Cada clase que herede de ella debe sobreponerla para realizar sus propias acciones
	 *   con el objetivo de darle un valor al objeto 'xmlResultado'.
	 *  
	 *  Par�metros: no tiene
	 *  Lanza excepci�n si: - se produce alg�n error realizando las acciones
	 */
	abstract protected function _ejecutaAcciones ();


	/*
	 *  m�todo getXML.
	 *
	 *  Obtiene la representaci�n de este objeto en un documento XML (XMLSimple).
	 *   Este m�todo pertenece a la interfaz iXML.
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: un objeto XMLSimple
	 *  Lanza excepci�n si: - error en la generaci�n del objeto
	 */
	public function getXML () {

		if ( is_null($this->xmlResultado) ) {
			throw new Excepcion('no se ha ejecutado ninguna acci�n', __METHOD__);
		}

		return $this->xmlResultado;

	}
	
}

?>