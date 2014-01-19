<?php

/*
 * Clase MenuSimple.
 *
 *  Representa un men� del sistema.
 *  Hereda de ItemConectable e implementa iJSON.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 16/01/2007
 */
class MenuSimple extends ItemConectable {

	// Conector a la base de datos
	private $conector;

	// Librer�a del sistema
	private $libreriaSistema;

	// C�digo encriptado de usuario
	private $usuario;

	// Indica si es superusuario
	private $bEsSuper;

	// Lector con los men�s
	private $lectorMenus;

	/*
	 * constructor.
	 *
	 *  Asigna las variables y obtiene la parametrizaci�n.
	 *  
	 *  Par�metros: $libreriaSistema: librer�a del sistema
	 *              $arrayVariables: array con las variables para la pantalla
	 *              $bEsCampoRelacionado: indica si es una pantalla para campos relacionados
	 *  Lanza excepci�n si: - error en la base de datos
	 *                      - error en la parametrizaci�n
	 */
	public function __construct ($libreriaSistema, $arrayVariables = false) { 

		// Llamada al constructor de la clase base
		parent::__construct($arrayVariables);

		// Conexi�n a base de datos
		$this->conector = $this->getConector(false);

		// Librer�a del sistema
		$this->libreriaSistema = $libreriaSistema;
		//if ( $this->libreriaSistema != '' )  $this->libreriaSistema .= '.';
		if ( $this->libreriaSistema != '' && !strpos($this->libreriaSistema, '.') ) {
			$this->libreriaSistema .= '.';
		}

		// Usuario
		if ( !$this->hayVariable('usuario') )  throw new Excepcion('falta el usuario', __METHOD__);
		$this->usuario = $this->getVariable('usuario');
		
		// Comprueba el usuario
		$this->bEsSuper = $this->compruebaUsuario();

		// Obtiene los men�s
		$this->lectorMenus = $this->getMenus();


	}

	/*
	 *  m�todo compruebaUsuario.
	 *
	 *  Comprueba si el c�digo de usuario encriptado es v�lido, adem�s de averiguar
	 *   si es el superusuario del sistema.
	 *  
	 *  Par�metros: no tiene
	 *  Devuelve: 'true' si es el superusuario del sistema y 'false' si no lo es
	 *  Lanza excepci�n si: - error al acceder a base de datos
	 *                      - usuario no v�lido
	 */
	protected function compruebaUsuario () {

		$lector = $this->conector->consulta("SELECT USUARIO FROM {$this->libreriaSistema}usuarios WHERE CUSUARIO='{$this->usuario}'");
		
		// Control de usuario existente
		if ( $lector->getNumRegistros() == 0 )  throw new Excepcion('El usuario no es v�lido', __METHOD__);

		$lector->siguiente();

		return ($lector->getValor('USUARIO') == 'SUPER');

	}

	// M�todos de acceso
	public function getUsuario () { return $this->usuario; }

	/*
	 *  m�todo getMenus.
	 *
	 *  Obtiene un lector con los men�s en los que hay opciones permitidas para el usuario.
	 *  
	 *  Par�metros: no tiene
	 *  Lanza excepci�n si: - error en la base de datos
	 */
	public function getMenus () {

		$consulta = ($this->bEsSuper ? "SELECT * FROM {$this->libreriaSistema}menus ORDER BY ORDEN" : "SELECT DISTINCT menus.* FROM {$this->libreriaSistema}submenus AS submenus INNER JOIN {$this->libreriaSistema}menus AS menus ON menus.CODMENU=submenus.CODMENU INNER JOIN {$this->libreriaSistema}funperfil AS funperfil ON funperfil.PANTALLA=submenus.PANTALLA INNER JOIN {$this->libreriaSistema}usrperfil AS usrperfil ON usrperfil.PERFIL=funperfil.PERFIL INNER JOIN {$this->libreriaSistema}usuarios AS usuarios ON usuarios.USUARIO=usrperfil.USUARIO WHERE usuarios.CUSUARIO='{$this->usuario}' AND funperfil.CONSULTA='S' ORDER BY menus.ORDEN");

		return $this->conector->consulta($consulta);

	}

	/*
	 *  m�todo pintaBarraMenus.
	 *
	 *  Pinta la barra de men�s.
	 *  
	 *  Par�metros: no tiene
	 *  Lanza excepci�n si: - error en la base de datos
	 */
	public function pintaBarraMenus () {

		$numMenus = $this->lectorMenus->getNumRegistros(); 
		$anchoTabla = 100;
		$anchoMax = 20;
		$ancho = min($anchoTabla / $numMenus, $anchoMax);
		$anchoExcedente = $anchoTabla - $ancho*$numMenus;

		$plantilla = '<td width="' . $ancho . '%" onclick="javascript: menu.mostrarMenu(this, \'{$CODMENU}\')" onmouseover="javascript: menu.divEmergente.ocultar(); this.className=\'clBarraMenusOver\';" onmouseout="javascript: this.className=\'\';">{$DESCRIPCION}</td>';
		$formateador = new FormateadorResultado();
		$formateador->setLectorResultado($this->lectorMenus);
		$formateador->setPlantilla($plantilla);

		echo '<table width="' . $anchoTabla .'%" class="clBarraMenus" cellspacing="1" cellpadding="4"><tr>';
		echo $formateador->getXML()->getContenido();
		if ( $anchoExcedente > 0 )  echo "<td width='{$anchoExcedente}%'></td>";
		echo '</tr></table>';


	}

	/*
	 *  m�todo pintaObjetosSubmenus.
	 *
	 *  Pinta la objetos JSON de los submenus.
	 *  
	 *  Par�metros: no tiene
	 *  Lanza excepci�n si: - error en la base de datos
	 */
	public function pintaObjetosSubmenus () {

		$this->lectorMenus->situarEnRegistro(0);
		while ( $this->lectorMenus->siguiente() ) {
			$menu = $this->lectorMenus->getValor('CODMENU');
			$consulta = ($this->bEsSuper ? "SELECT *, 'S' AS CONSULTA FROM {$this->libreriaSistema}submenus WHERE CODMENU={$menu} ORDER BY ORDEN" : "SELECT DISTINCT submenus.*, CASE submenus.PANTALLA WHEN 'PANTALLAS' THEN 'N' ELSE funperfil.CONSULTA END AS CONSULTA FROM {$this->libreriaSistema}submenus AS submenus LEFT JOIN {$this->libreriaSistema}funperfil AS funperfil ON funperfil.PANTALLA=submenus.PANTALLA LEFT JOIN {$this->libreriaSistema}usrperfil AS usrperfil ON usrperfil.PERFIL=funperfil.PERFIL INNER JOIN {$this->libreriaSistema}usuarios as usuarios ON usuarios.USUARIO=usrperfil.USUARIO AND usuarios.CUSUARIO='{$this->usuario}' WHERE submenus.CODMENU={$menu} ORDER BY submenus.ORDEN");
			$lector = $this->conector->consulta($consulta);
			$formateador = new FormateadorResultado();
			$formateador->setLectorResultado($lector);
			$json = $formateador->getXML()->toJSON();
			$json = str_replace("\\", "\\\\", $json);
			$json = str_replace("'", "\'", $json);
			echo "var objMenu$menu = eval('(" . $json . ")');\n";
		}

	}

}

?>