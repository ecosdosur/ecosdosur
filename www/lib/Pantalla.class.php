<?php

/*
 * Clase Pantalla.
 *
 *  Representa una pantalla del parametrizador de pantallas (el modelo del esquema MVC).
 *  Hereda de ItemConectable e implementa iJSON.
 *
 *  Una pantalla puede tener tres modos de funcionamiento: 
 *    - (#1) para vista y/o edición de una tabla
 *    - (#2) para rellenar campos relacionados de una pantalla como la anterior
 *    - (#3) lista para campos relacionados en diferido de una pantalla como la primera
 *
 *  IMPORTANTE: el módulo que incluye un objeto de esta clase (en principio, 'vista.php' y 'edicion.php') ha de tener
 *    una serie de variables globales definidas para su correcto funcionamiento:
 *    - $_db (*): array con las diferentes conexiones del sistema y los parámetros de cada una de ellas, y
 *    - $_cnx (*): nombre de la conexión por defecto.
 *    - $_clave: clave para realizar la encriptación de usuarios y contraseñas
 *   (*) Las dos primeras variables se usan en el método getConector() de la clase base
 *
 *  Autor: Pentared
 *  Última actualización: 22/01/2007
 */
class Pantalla extends ItemConectable implements iJSON {

	// Conector a la base de datos
	private $conector;

	// Librería del sistema
	private $libreriaSistema;

	// Código de la pantalla
	private $pantalla;

	// Libreria.tabla de la pantalla
	private $descripcion;

	// Libreria.tabla de la pantalla
	private $libreriaTabla;

	// Array de XMLSimple con los campos de gestión
	private $arrayCamposGestion;

	// Consulta de resultado
	private $selectResultado;

	// Array de XMLSimple con los campos de resultado
	private $arrayCamposResultado;

	// Array de XMLSimple con las relacionadas
	private $arrayRelacionadas;

	// Objetos de esta misma clase con la parametrización de las tablas de campos relacionados
	private $arrayPantallasCamposRelacionados;
	// Array con los nombre de los campos relacionados correspondientes a las pantallas indicadas
	private $arrayCamposRelacionados;

	// Fichero de edición específica
	private $edicionEspec;

	// Fichero de JavaScript específico
	private $jsEspec;

	// Código encriptado de usuario
	private $usuario;

	// Permiso de modificación
	private $bModificar;

	// Indica si es una pantalla de campos relacionados
	private $bEsCampoRelacionado;

	// Variables de orden
	private $campoOrden;
	private $tipoOrden;

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
	public function __construct ($libreriaSistema, $arrayVariables = false, $bEsCampoRelacionado = false) { 

		// Llamada al constructor de la clase base
		parent::__construct($arrayVariables);

		// Conexión a base de datos
		$this->conector = $this->getConector(false);

		// Librería del sistema
		$this->libreriaSistema = $libreriaSistema;
		if ( $this->libreriaSistema != '' && !strpos($this->libreriaSistema, '.') ) {
			$this->libreriaSistema .= '.';
		}

		// Obtención de la parametrización
		$this->getParametrizacion();

		// Obtención de los permisos y relacionadas: sólo en caso de pantalla estándar (pantalla #1)
		$this->bEsCampoRelacionado = $bEsCampoRelacionado;
		if ( !$this->bEsCampoRelacionado && ($this->getVariable('lista') != 'S') ) {
			$this->getPermisos();
			$this->getPantallasCamposRelacionados();
		}

	}

	/*
	 *  método getParametrizacion.
	 *
	 *  Obtiene la parametrización de esta pantalla.
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - error en la base de datos
	 *                      - error en la parametrización
	 */
	private function getParametrizacion () {

		// Código de la pantalla
		if ( !$this->hayVariable('pantalla') ) {
			throw new Excepcion("No se ha enviado el parámetro 'pantalla'", __METHOD__);
		}
		$this->pantalla = $this->getVariable('pantalla');

		// Consulta de la parametrización
		$sqlParametrizacion = "SELECT * FROM {$this->libreriaSistema}pantallas WHERE PANTALLA='{$this->pantalla}'";
		$lector = $this->conector->consulta($sqlParametrizacion);
		if ( $lector->getNumRegistros() == 0 ) {
			throw new Excepcion("No se encuentra la pantalla '{$this->pantalla}'", __METHOD__);
		}

		// Asignación de variables
		$lector->siguiente();
		$this->descripcion = $lector->getValor('DESCRIPCION');
		$this->libreriaTabla = $lector->getValor('LIBTABLA');
		$this->selectResultado = $lector->getValor('SELECTRES');
		$this->edicionEspec = $lector->getValor('EDICIONESPEC');
		$this->jsEspec = $lector->getValor('JSESPEC');

		// Campos de gestión
		$this->arrayCamposGestion = null;
		$camposGestion = $lector->getValor('CAMPOSGES');
		if ( $camposGestion != '' ) {
			$this->arrayCamposGestion = array();
			$listaCamposGestion = explode(' ', $camposGestion);
			foreach ( $listaCamposGestion as $cadenaCampo ) {
				if ( $cadenaCampo == '' )  continue;
				$listaCampo = explode('|', $cadenaCampo);
				if ( sizeof($listaCampo) != 4 ) {
					throw new Excepcion("Formato de campo de gestión irregular ($cadenaCampo)", __METHOD__);
				}
				$xmlCampo = new XMLSimple('campo', false);
				$xmlCampo->setNodoRaiz(false);
				$xmlCampo->addVariable('nombre', $listaCampo[0]);
				$xmlCampo->addVariable('descripcion', str_replace('_', ' ', $listaCampo[1]));
				$xmlCampo->addVariable('obligatorio', $listaCampo[2]);
				$arrayTipo = explode('_', $listaCampo[3]);
				$extraTipo = (sizeof($arrayTipo) > 1 ? $arrayTipo[1] : '');
				$arrayExtraTipo = explode('#', $extraTipo);
				$relacionado = (is_numeric($arrayExtraTipo[0]) ? '' : $arrayExtraTipo[0]);
				$descRelacionado = (sizeof($arrayExtraTipo) > 1 ? $arrayExtraTipo[1] : '');
				$xmlCampo->addVariable('tipo', $arrayTipo[0]);
				//$xmlCampo->addVariable('extraTipo', $extraTipo);
				$xmlCampo->addVariable('longitud', ((sizeof($arrayTipo) > 1) && is_numeric($arrayTipo[1]) ? $arrayTipo[1] : ''));
				$xmlCampo->addVariable('relacionado', $relacionado);
				$xmlCampo->addVariable('descRelacionado', $descRelacionado);
				$this->arrayCamposGestion[] = $xmlCampo;
			}
		}

		// Campos de resultado
		$this->arrayCamposResultado = null;
		$camposResultado = $lector->getValor('CAMPOSRES');
		if ( $camposResultado != '' ) {
			$this->arrayCamposResultado = array();
			$listaCamposResultado = explode(' ', $camposResultado);
			foreach ( $listaCamposResultado as $cadenaCampo ) {
				if ( $cadenaCampo == '' )  continue;
				$listaCampo = explode('|', $cadenaCampo);
				if ( sizeof($listaCampo) != 5 ) {
					throw new Excepcion("Formato de campo de resultado irregular ($cadenaCampo)", __METHOD__);
				}
				$xmlCampo = new XMLSimple('campo', false);
				$xmlCampo->setNodoRaiz(false);
				$xmlCampo->addVariable('nombre', $listaCampo[0]);
				$xmlCampo->addVariable('alias', $listaCampo[1]);
				$xmlCampo->addVariable('descripcion', str_replace('_', ' ', $listaCampo[2]));
				$arrayTipo = explode('#', $listaCampo[3]);
				$selected = (sizeof($arrayTipo) > 1 ? 'S' : 'N');
				$xmlCampo->addVariable('tipo', $arrayTipo[0]);
				$xmlCampo->addVariable('selected', $selected);
				$xmlCampo->addVariable('ancho', $listaCampo[4]);
				$this->arrayCamposResultado[] = $xmlCampo;
				if ( $selected == 'S' )  $this->campoOrden = $listaCampo[0];
			}
		}
		
		// Relacionadas
		$this->arrayRelacionadas = null;
		$relacionadas = $lector->getValor('RELACIONADAS');
		if ( $relacionadas != '' ) {
			$this->arrayRelacionadas = array();
			$listaRelacionadas = explode(' ', $relacionadas);
			foreach ( $listaRelacionadas as $cadenaCampo ) {
				if ( $cadenaCampo == '' )  continue;
				$listaCampo = explode('|', $cadenaCampo);
				if ( sizeof($listaCampo) != 4 ) {
					throw new Excepcion("Formato de relacionadas irregular ($cadenaCampo)", __METHOD__);
				}
				$xmlCampo = new XMLSimple('relacionada', false);
				$xmlCampo->setNodoRaiz(false);
				$xmlCampo->addVariable('nombre', $listaCampo[0]);
				$xmlCampo->addVariable('descripcion', $listaCampo[1]);
				$xmlCampo->addVariable('permiso', 'N');
				$xmlCampo->addVariable('campos', $listaCampo[2]);
				$xmlCampo->addVariable('descPadre', $listaCampo[3]);
				$this->arrayRelacionadas[] = $xmlCampo;
			}
		}

		// Campo y tipo de orden (parámetros de llamada)
		if ( $this->hayVariable('campoOrden') )  $this->campoOrden = $this->getVariable('campoOrden');
		if ( ($this->campoOrden == '') && (!is_null($this->arrayCamposResultado)) )  $this->campoOrden = $this->arrayCamposResultado[0]->getVariable('nombre');
		if ( $this->hayVariable('tipoOrden') )  $this->tipoOrden = $this->getVariable('tipoOrden');
		if ( $this->tipoOrden == '' )  $this->tipoOrden = 'ASC';

	}


	/*
	 *  método getPermisos.
	 *
	 *  Obtiene los permisos para esa pantalla y sus relacionadas
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - error en la base de datos
	 *                      - error en los parámetros
	 */
	private function getPermisos () {

		// Código de usuario
		if ( !$this->hayVariable('usuario') ) {
			throw new Excepcion("No se ha enviado el parámetro 'usuario'", __METHOD__);
		}
		$this->usuario = $this->getVariable('usuario');

		// Permiso de esta pantalla
		list($bConsultar, $this->bModificar) = $this->getPermiso($this->pantalla);

		if ( !$bConsultar )  throw new Excepcion('El usuario no tiene permisos para esta pantalla', __METHOD__);

		// Permiso de las relacionadas
		foreach ( $this->arrayRelacionadas as $xmlRelacionada ) {
			list($permiso, ) = $this->getPermiso($xmlRelacionada->getVariable('nombre'));
			$xmlRelacionada->addVariable('permiso', ($permiso ? 'S' : 'N'));
		}

	}

	/*
	 *  método getPermisos.
	 *
	 *  Obtiene los permisos para la pantalla indicada
	 *  
	 *  Parámetros: $pantalla para obtener los permisos
	 *  Lanza excepción si: - error en la base de datos
	 *                      - no se encuentra una variable glonbal
	 */
	private function getPermiso ($pantalla) {

		global $_clave;

		// Control de clave
		if ( !isset($_clave) )  throw new Excepcion('no se ha definido la variable global $_clave', __METHOD__);

		// Control de superusuario
		if ( $this->usuario == crypt('SUPER', $_clave) )  return array(true, true);

		// Consulta de permisos
		$sqlPermisos = "SELECT A.CONSULTA, A.MODIFICACION FROM {$this->libreriaSistema}funperfil AS A INNER JOIN {$this->libreriaSistema}perfiles AS B ON B.PERFIL=A.PERFIL INNER JOIN {$this->libreriaSistema}usrperfil AS C ON C.PERFIL=B.PERFIL INNER JOIN {$this->libreriaSistema}usuarios AS D ON D.USUARIO=C.USUARIO WHERE D.CUSUARIO='{$this->usuario}' AND A.PANTALLA='$pantalla'";
		$lector = $this->conector->consulta($sqlPermisos);

		// No se encuentra = no tiene permisos
		if ( $lector->getNumRegistros() == 0 )  return array(false, false);

		// Se encuentra: leerlos
		$lector->siguiente();
		$bConsulta = ($lector->getValor('CONSULTA') == 'S');
		$bModificacion = ($lector->getValor('MODIFICACION') == 'S');

		return array($bConsulta, $bModificacion);

	}

	/*
	 *  método getPantallasCamposRelacionados.
	 *
	 *  Obtiene la parametrización de las pantallas que representan campos relacionados.
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - error en las pantallas
	 */
	private function getPantallasCamposRelacionados () {

		$this->arrayPantallasCamposRelacionados = array();
		$this->arrayCamposRelacionados = array();

		foreach ( $this->arrayCamposGestion as $xmlCampo ) {
			$nombre = $xmlCampo->getVariable('relacionado');
			$campoDesc = $xmlCampo->getVariable('descRelacionado');
			if ( ($nombre == '') || ($campoDesc != '') )  continue;
			$arrayVariables['pantalla'] = $nombre;
			$this->arrayPantallasCamposRelacionados[] = new Pantalla($this->libreriaSistema, $arrayVariables, true);
			$this->arrayCamposRelacionados[] = $xmlCampo->getVariable('nombre');
		}

	}

	// Métodos de obtención
	public function getNombre () { return $this->pantalla; }
	public function getDescripcion () { return $this->descripcion; }
	public function getSelectResultado () { return $this->selectResultado; }
	public function getCampoOrden () { return $this->campoOrden; }
	public function getTipoOrden () { return $this->tipoOrden; }

	/*
	 *  método pintaScripts.
	 *
	 *  Pinta los scripts específicos.
	 *  
	 *  Parámetros: no tiene
	 */
	public function pintaScripts () {

		// Por ahora, sólo se considera un solo script
		if ( $this->jsEspec != "" ) {
			echo '<script language="JavaScript" type="text/javascript" src="' . $this->jsEspec . '"></script>' . "\n";
		}

	}

	/*
	 *  método pintaPlantillaCampos.
	 *
	 *  Pinta la plantilla de los campos para la edición.
	 *  
	 *  Parámetros: no tiene
	 *  Lanza excepción si: - no encuentra el fichero específico
	 */
	public function pintaPlantillaCampos () {

		if ( $this->edicionEspec != '' ) {
			if ( !include_once($this->edicionEspec)	) {
				throw new Excepcion("no se encuentra el fichero '{$this->edicionEspec}'", __METHOD__);
			}
			return;
		}

		echo "<table border='0' cellpadding='2' cellspacing='2' align='center' class='clTablaEdicion'>\n";
		foreach ( $this->arrayCamposGestion as $xmlCampo ) {
			$nombreCampo = $xmlCampo->getVariable('nombre');
			$obligatorio = $xmlCampo->getVariable('obligatorio');
			$tipo = $xmlCampo->getVariable('tipo');
			$relacionado = $xmlCampo->getVariable('relacionado');
			$longitud = $xmlCampo->getVariable('longitud');
			$descRelacionado = $xmlCampo->getVariable('descRelacionado');
			$readonly = ($obligatorio == 'A' || !$this->bModificar ? "readonly='readonly'" : '');
			$disabled = ($readonly == '' ? '' : 'disabled');
			$clase = (($obligatorio == 'N') || ($obligatorio == 'A') || !$this->bModificar ? 'inputTexto' : 'inputTextoObligatorio');
			echo '<tr><td align="right">' . $xmlCampo->getVariable('descripcion') . '</td>';
			echo '<td align="left">';
			if ( $relacionado == '' ) {
				switch ( $tipo ) {
					case 'A': {
						// Tipo cadena alfanumérica
						if ( $longitud > 100 ) {
							// Campo extenso
							echo "<textarea id='$nombreCampo' class='$clase' rows='5' cols='50'  onkeypress=\"javascript: return edicion.controlMaxLength(this, event, $longitud);\" $readonly></textarea>";
						}
						else {
							// Campo normal
							echo "<input type='text' id='$nombreCampo' size='{$longitud}' maxlength='{$longitud}' class='$clase' $readonly />";
						}
						break;
					}
					case 'C': {
						// Check
						echo "<input type='checkbox' id='{$nombreCampo}' $disabled />";
						break;
					}
					case 'S': {
						// Radio: 'Sí' o 'No'
						echo "<input type='radio' id='{$nombreCampo}_S' onclick='javascript: this.checked = true; document.getElementById(\"{$nombreCampo}_N\").checked = false;' $disabled />Sí";
						echo "<input type='radio' id='{$nombreCampo}_N' onclick='javascript: this.checked = true; document.getElementById(\"{$nombreCampo}_S\").checked = false;' $disabled />No";
						break;
					}
					case 'G': {
						// Radio: 'Hombre' o 'Mujer'
						echo "<input type='radio' id='{$nombreCampo}_H' onclick='javascript: this.checked = true; document.getElementById(\"{$nombreCampo}_M\").checked = false;' $disabled />Hombre";
						echo "<input type='radio' id='{$nombreCampo}_M' onclick='javascript: this.checked = true; document.getElementById(\"{$nombreCampo}_H\").checked = false;' $disabled />Mujer";
						break;
					}
					case 'F': {
						// Fecha (ISO)
						//echo "<table cellspacing='0' cellpadding='0'><tr><td><input type='text' id='$nombreCampo' size='10' maxlengt='10' class='$clase' style='text-align: center' $readonly />&nbsp;</td><td><img src='img/calendario20.gif' style='cursor: pointer;' align='baseline' onclick=\"return showCalendar('$nombreCampo', '%d/%m/%Y');\"/></td></tr></table>";
						echo "<input type='text' id='$nombreCampo' size='12' maxlength='10' class='$clase' style='text-align: center' $readonly />";
						if ( $this->bModificar )  echo "<img id='cal_$nombreCampo' src='img/calendario20.gif' style='cursor: pointer;' onclick=\"return showCalendar('$nombreCampo', '%d/%m/%Y');\"/>";
						break;
					}
					case 'D': 
					default: {
						// Numérico o cualquier otro
						echo "<input type='text' id='$nombreCampo' size='20' class='$clase' $readonly />";
					}
				}
			}
			else if ( $descRelacionado == '' ) {
				// Campo relacionado directo
				echo "<select id='$nombreCampo' class='$clase' $disabled><option value=''></option></select>";
			}
			else {
				// Campo relacionado en diferido
				echo "<input type='text' id='$nombreCampo' size='15' class='$clase' readonly='readonly' /><input type='text' id='$descRelacionado' size='30' class='$clase' readonly='readonly' />";
				if ( $this->bModificar ) {
					echo "<input type='button' class='boton' style='width: 30px;' id='btn_$nombreCampo' value='...' onmouseover=\"javascript: if ( !this.disabled ) { this.className='botonOver'; };\" onmouseout=\"javascript: this.className='boton';\" onclick=\"javascript: edicion.abrirLista('$relacionado', '$nombreCampo|$descRelacionado');\" />";
					echo "<input type='button' class='boton' style='width: 30px;' id='btnDel_$nombreCampo' value=' X ' onmouseover=\"javascript: if ( !this.disabled ) { this.className='botonOver'; };\" onmouseout=\"javascript: this.className='boton';\" onclick=\"javascript: edicion.borrarRelacionado('$nombreCampo', '$descRelacionado');\" />";
				}
			}
			echo "</td></tr>\n";
		}
		echo "</table>\n";

		//echo $plantilla;

	}

	/*
	 *  método pintaObjetosCamposRelacionados.
	 *
	 *  Pinta los objetos JSON correspondientes a los campos relacionados
	 *  
	 *  Parámetros: no tiene
	 */
	public function pintaObjetosCamposRelacionados () {

		if ( sizeof($this->arrayPantallasCamposRelacionados) == 0 )  return;

		//$formateador = new FormateadorResultado();
		$k = 0;
		$camposEspecificos = array('', 'DESCRIPCION');
		foreach ( $this->arrayPantallasCamposRelacionados as $pantallaCampoRelacionado ) {
			$camposEspecificos[0] = $this->arrayCamposRelacionados[$k];
			$sql = $pantallaCampoRelacionado->getSelectResultado();
			$sql .= ' ORDER BY ' . $pantallaCampoRelacionado->getCampoOrden() . ' ' . $pantallaCampoRelacionado->getTipoOrden();
			$lector = $this->conector->consulta($sql);
			$formateador = new FormateadorResultado();
			$formateador->setCamposEspecificos($camposEspecificos);
			$formateador->setLectorResultado($lector);
			$json = $formateador->getXML()->toJSON();
			$nombreObjeto = 'obj' . $this->arrayCamposRelacionados[$k];
			$json = str_replace("\\", "\\\\", $json);
			$json = str_replace("'", "\'", $json);
			$json = str_replace("\n", "", $json);
			echo "var $nombreObjeto = eval('(" . $json . ")');\n";
			$k++;
		}

	}

	/*
	 *  método iJSON.
	 *
	 *  Convierte este objeto en una cadena que representa un objeto JavaScript
	 *   Este método proviene de implementar la interfaz iJSON.
	 *  
	 *  Parámetros: no tiene
	 *  Devuelve: cadena que representa la pantalla como objeto JavaScript.
	 */
	public function toJSON () {

		// "Nodo raíz"
		$json = '{';
		$json .= '"pantalla": {';

		// Atributos de base de datos: 'nombre', 'descripcion', 'libreriaTabla', 'selectResultado', 'edicionEspecifica', 'jsEspecifico'
		$json .= '"nombre": "' . $this->pantalla . '", ';
		$json .= '"descripcion": "' . str_replace('"', '\"', $this->descripcion) . '", ';
		$json .= '"libreriaTabla": "' . $this->libreriaTabla . '", ';
		$json .= '"selectResultado": "' . str_replace('"', '\"', $this->selectResultado) . '", ';
		$json .= '"edicionEspecifica": "' . str_replace('"', '\"', $this->edicionEspec) . '", ';
		$json .= '"jsEspecifico": "' . str_replace('"', '\"', $this->jsEspec) . '", ';

		// Atributos de usuario
		$json .= '"usuario": "' . str_replace('"', '\"', $this->usuario) . '", ';
		$json .= '"permisoModificar": "' . ($this->bModificar ? 'S' : 'N') . '", ';

		// Atributos por parámetro: 'campoOrden', 'tipoOrden', 'clave', 'condicionFija', 'camposFijos', 'valoresFijos', 'descPadreRelacionada', 'lista', 'camposLista'
		$json .= '"campoOrden": "' . $this->campoOrden . '", ';
		$json .= '"tipoOrden": "' . $this->tipoOrden . '", ';
		$json .= '"clave": "' . str_replace('"', '\"', $this->getVariable('clave')) . '", ';
		$json .= '"condicionFija": "' . str_replace('"', '\"', $this->getVariable('condicionFija')) . '", ';
		$json .= '"camposFijos": "' . str_replace('"', '\"', $this->getVariable('camposFijos')) . '", ';
		$json .= '"valoresFijos": "' . str_replace('"', '\"', $this->getVariable('valoresFijos')) . '", ';
		$json .= '"descPadreRelacionada": "' . str_replace('"', '\"', $this->getVariable('descPadreRelacionada')) . '", ';
		$json .= '"lista": "' . str_replace('"', '\"', $this->getVariable('lista')) . '", ';
		$json .= '"camposLista": "' . str_replace('"', '\"', $this->getVariable('camposLista')) . '"';

		// Campos de gestión
		if ( !is_null($this->arrayCamposGestion) ) {
			$json .= ', "camposGestion": [';
			$arrayNodosJSON = array();
			foreach ( $this->arrayCamposGestion as $nodo ) {
				$arrayNodosJSON[] = $nodo->toJSON();
			}
			$json .= implode(', ', $arrayNodosJSON);
			$json .= ']';
		}

		// Campos de resultado
		if ( !is_null($this->arrayCamposResultado) ) {
			$json .= ', "camposResultado": [';
			$arrayNodosJSON = array();
			foreach ( $this->arrayCamposResultado as $nodo ) {
				$arrayNodosJSON[] = $nodo->toJSON();
			}
			$json .= implode(', ', $arrayNodosJSON);
			$json .= ']';
		}

		// Relacionadas
		if ( !is_null($this->arrayRelacionadas) ) {
			$json .= ', "relacionadas": [';
			$arrayNodosJSON = array();
			foreach ( $this->arrayRelacionadas as $nodo ) {
				$arrayNodosJSON[] = $nodo->toJSON();
			}
			$json .= implode(', ', $arrayNodosJSON);
			$json .= ']';
		}

		// Cierre del "nodo raíz"
		$json .= '}';
		$json .= '}';

		// Se debe usar para colocar entre comillas simples
		$json = str_replace("\\", "\\\\", $json);
		return str_replace("'", "\'", $json);

	}

}

?>