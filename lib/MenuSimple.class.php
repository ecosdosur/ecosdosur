<?php

/*
 * Clase MenuSimple.
 *
 *  Representa un menú del sistema.
 *  Hereda de ItemConectable e implementa iJSON.
 *
 *  Autor: Pentared
 *  Última actualización: 16/01/2007
 */
class MenuSimple extends ItemConectable {

	// Conector a la base de datos
	private $conector;

	// Librería del sistema
	private $libreriaSistema;

	// Código encriptado de usuario
	private $usuario;

	// Indica si es superusuario
	private $bEsSuper;

	// Lector con los menús
	private $lectorMenus;

	/*
	 * constructor.
	 *
	 *  Asigna las variables y obtiene la parametrización.
	 *  
	 *  Parámetros: $libreriaSistema: librería del sistema
	 *              $arrayVariables: array con las variables para la pantalla
	 *              $bEsCampoRelacionado: indica si es una pantalla para campos relacionados
	 *  Lanza excepción si: - error en la base de datos
	 *                      - error en la parametrización
	 */
	public function __construct ($libreriaSistema, $arrayVariables = false) { 

		// Llamada al constructor de la clase base
		parent::__construct($arrayVariables);

		// Conexión a base de datos
		$this->conector = $this->getConector(false);

		// Librería del sistema
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

		// Obtiene los menús
		$this->lectorMenus = $this->getMenus();


	}

	/*
	 *  método compruebaUsuario.
	 *
	 *  Comprueba si el código de usuario encriptado es válido, además de averiguar
	 *   si es el superusuario del sistema.
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: 'true' si es el superusuario del sistema y 'false' si no lo es
	 *  Lanza excepción si: - error al acceder a base de datos
	 *                      - usuario no válido
	 */
	protected function compruebaUsuario () {

		$lector = $this->conector->consulta("SELECT USUARIO FROM {$this->libreriaSistema}usuarios WHERE CUSUARIO='{$this->usuario}'");
		
		// Control de usuario existente
		if ( $lector->getNumRegistros() == 0 )  throw new Excepcion('El usuario no es válido', __METHOD__);

		$lector->siguiente();

		return ($lector->getValor('USUARIO') == 'SUPER');

	}

	// Métodos de acceso
	public function getUsuario () { return $this->usuario; }

	/*
	 *  método getMenus.
	 *
	 *  Obtiene un lector con los menús en los que hay opciones permitidas para el usuario.
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - error en la base de datos
	 */
	public function getMenus () {

		$consulta = ($this->bEsSuper ? "SELECT * FROM {$this->libreriaSistema}menus ORDER BY ORDEN" : "SELECT DISTINCT menus.* FROM {$this->libreriaSistema}submenus AS submenus INNER JOIN {$this->libreriaSistema}menus AS menus ON menus.CODMENU=submenus.CODMENU INNER JOIN {$this->libreriaSistema}funperfil AS funperfil ON funperfil.PANTALLA=submenus.PANTALLA INNER JOIN {$this->libreriaSistema}usrperfil AS usrperfil ON usrperfil.PERFIL=funperfil.PERFIL INNER JOIN {$this->libreriaSistema}usuarios AS usuarios ON usuarios.USUARIO=usrperfil.USUARIO WHERE usuarios.CUSUARIO='{$this->usuario}' AND funperfil.CONSULTA='S' ORDER BY menus.ORDEN");

		return $this->conector->consulta($consulta);

	}

	/*
	 *  método pintaBarraMenus.
	 *
	 *  Pinta la barra de menús.
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - error en la base de datos
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
	 *  método pintaObjetosSubmenus.
	 *
	 *  Pinta la objetos JSON de los submenus.
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - error en la base de datos
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