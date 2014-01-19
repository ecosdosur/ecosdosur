/*
 * Clase Vista.
 *
 *  Representa las principales operaciones relacionadas con la vista de una pantalla.
 *
 *  IMPORTANTE: debe tener incluido antes los siguientes ficheros: 'String.js', 'AJAXRequest.js',
 *   'Ventana.js' y 'Fecha.js'
 * 
 *  Autor: Pentared
 *  Última actualización: 30/01/2007
 */

/*
 * Constructor de la clase
 *
 *  Realiza las operaciones iniciales para la gestión de una vista: generación de las plantillas
 *   de la clave, cabecera y línea de resultado; inicialización de variables y preparación del 
 *   div emergente del menú contextual.
 *
 *  Parámetros: pantalla: código de la pantalla: 
 *  Lanza excepción si: - error en el div emergente
 */
function Vista (pantalla) {
	
	// Asignación de objeto de pantalla
	this.pantalla = pantalla;

	// Indica si esta pantalla es una lista
	this.esLista = (this.pantalla.lista == "S");

	// Plantilla de clave
	this.clave = this.getClave();

	// Construcción de la cabecera
	this.cabecera = this.getCabecera();
	//alert(this.cabecera);

	// Construcción de la plantilla de resultado
	this.plantilla = this.getPlantilla();
	//alert(this.plantilla);

	// Contadores de resultado
	this.filaInicial = 0;
	this.numFilas = 0;
	this.numResultados = 0;
	this.numFilasPorPagina = (this.esLista ? 10 : 15);   // ¿Parametrizable? ¿Editable?

	// Número de filtros
	this.numFiltros = 0;

	// Última condición de búsqueda y su descripción
	this.condicionBusqueda = this.pantalla.condicionFija;
	this.textoCondicionBusqueda = this.pantalla.descPadreRelacionada;

	// Div emergente para el menú de cada registro y las opciones generales
	this.menu = new DivEmergente("menu", "bordeMenu");
	this.menu.posRel = "r";
	this.menu.offsetExtra = 7;

}

/*
 * Método getClave()
 *
 *  Obtiene una cadena con la plantilla de la clave de cada registro del resultado.
 *
 *  Parámetros: no tiene
 *  Devuelve: cadena con la plantilla de la clave de cada registro del resultad
 */
Vista.prototype.getClave = function () {

	var libtabla = this.pantalla.libreriaTabla.split(".");
	var tabla = (libtabla.length == 1 ? libtabla[0] : libtabla[1]);

	var arrayClave = new Array();
	var objCampo = null;
	var tipo, clave;
	var nombreCampo, valorCampo
	if ( this.pantalla.camposGestion ) {
		for ( var k in this.pantalla.camposGestion ) {
			objCampo = this.pantalla.camposGestion[k];
			if ( (objCampo.obligatorio != "C") && (objCampo.obligatorio != "A") )  continue;
			nombreCampo = (tabla == "" ? objCampo.nombre : tabla + "." + objCampo.nombre);
			valorCampo = (tabla == "" ? objCampo.nombre.substring(objCampo.nombre.indexOf(".") + 1) : objCampo.nombre);
			tipo = objCampo.tipo.charAt(0);
			clave = nombreCampo + "=";
			if ( tipo == "A" )  clave += "'{$" + valorCampo + "}'";
			else  clave += "{$" + valorCampo + "}";
			arrayClave.push(clave);
		}
	}

	return arrayClave.join(" AND ");

}

/*
 * Método getCabecera()
 *
 *  Obtiene una cadena con la cabecera del resultado.
 *
 *  Parámetros: no tiene
 *  Devuelve: cadena con la cabecera del resultado.
 */
Vista.prototype.getCabecera = function () {

	var cabecera = '<tr class="clCabeceraResultado">';
	cabecera += '<td width="1px" class="clTablaOut" align="right">';
	if ( !this.esLista ) {
		cabecera += '<span onclick="javascript: vista.verAcciones(this);" title="Acciones sobre esta vista"><font face="Webdings" size="3">a</font></span>';
	}
	cabecera += '</td>';

	var objCampo = null;
	if ( this.pantalla.camposResultado ) {
		for ( var k in this.pantalla.camposResultado ) {
			objCampo = this.pantalla.camposResultado[k];
			//cabecera += '<td colspan="' + objCampo.ancho + '" align="center">';
			cabecera += '<td width="' + objCampo.ancho + '%" align="center">';
			cabecera += '<span>';
			cabecera += '<span id="nombre' + objCampo.nombre + '" onmouseover="javascript: this.className=\'clCabeceraOver\';" onmouseout="javascript: this.className=\'\';" onclick="javascript: vista.ordena(\'' + objCampo.nombre + '\');">' + objCampo.descripcion + '&nbsp;</span>';
			cabecera += '<span id="icoOrden' + objCampo.nombre + '"></span>';
			cabecera += '</span>';
			cabecera += '</td>';
		}
	}

	cabecera += '</tr>';

	return cabecera;

}

/*
 * Método getPlantilla()
 *
 *  Obtiene una cadena con la plantilla de una línea del resultado.
 *
 *  Parámetros: no tiene
 *  Devuelve: cadena con la plantilla de una línea del resultado.
 */
Vista.prototype.getPlantilla = function () {

	// Clave
	//var clave = this.clave.replace(/\'/g, "´");
	var clave = this.clave;

	// Campos fijos para relacionadas y descripción de padre
	var arrayCamposRelacionadas = new Array();
	var arrayValoresRelacionadas = new Array();
	var arrayDescPadreRelacionadas = new Array();
	if ( this.pantalla.relacionadas ) {
		var objRelacionada = null;
		for ( var k in this.pantalla.relacionadas ) {
			objRelacionada = this.pantalla.relacionadas[k];
			arrayDescPadreRelacionadas.push(objRelacionada.descPadre.replace(/_/g, " "));
			arrayCampos = objRelacionada.campos.split("#");
			for ( var n in arrayCampos ) {
				arrayCamposRelacionadas.push(arrayCampos[n]);
				arrayValoresRelacionadas.push("{$" + arrayCampos[n] + "}");
			}
		}
	}

	var plantilla = '<tr class="clFila" onmouseover="javascript: this.className=\'clFilaOver\';" onmouseout="javascript: this.className=\'clFila\';">';
	if ( !this.esLista ) {
		// Si no es una lista, se muestra un icono para desplegar el menú de opciones
		plantilla += '<td class="clTablaOut" style="text-align: right;" title="Acciones sobre este registro"><input type="hidden" id="clave_{$_numRegistro}" value="' + clave + '" /><input type="hidden" id="camposRelacionadas_{$_numRegistro}" value="' + arrayCamposRelacionadas.join("|") + '" /><input type="hidden" id="valoresRelacionadas_{$_numRegistro}" value="' + arrayValoresRelacionadas.join("|") + '" /><input type="hidden" id="descPadreRelacionadas_{$_numRegistro}" value="' + arrayDescPadreRelacionadas.join("|") + '" /><span onclick="javascript: vista.verMenu(this, \'{$_numRegistro}\');"><font face="Webdings" size="3">i</font></span></td>';
	}
	else {
		// Si es una lista, es el botón para obtener dicho campo
		var arrayCampos = this.pantalla.camposLista.split("|");
		var arrayValores = new Array();
		for ( var k in arrayCampos ) {
			arrayValores.push("{$" + arrayCampos[k] + "}")
		}
		// OJO!!!! con pipelines
		plantilla += '<td class="clTablaOut" style="text-align: right;" title="Seleccionar"><input type="hidden" id="camposLista_{$_numRegistro}" value="' + this.pantalla.camposLista + '" /><input type="hidden" id="valoresLista_{$_numRegistro}" value="' + arrayValores.join("|") + '" /><span onclick="javascript: vista.volverLista(\'{$_numRegistro}\');"><font face="Webdings" size="3">a</font></span></td>';
	}

	var objCampo = null;
	var alineamiento = "";
	if ( this.pantalla.camposResultado ) {
		for ( var k in this.pantalla.camposResultado ) {
			objCampo = this.pantalla.camposResultado[k];
			switch ( objCampo.tipo ) {
				case "D": { alineamiento = "right"; break; }
				case "F": { alineamiento = "center"; break; }
				case "C", "S", "G", "A":
				default: { alineamiento = "left"; }
			}
			//plantilla += '<td colspan="' + objCampo.ancho + '" align="' + alineamiento + '">';
			plantilla += '<td align="' + alineamiento + '">';
			plantilla += '{$' + objCampo.alias + '}';
			plantilla += '</td>';
		}
	}

	plantilla += '</tr>';
	//alert(plantilla);

	return plantilla;

}

/*
 * Método inicio()
 *
 *  Realiza las operaciones iniciales tras la carga de la página: crear el filtro y realizar
 *   la primera búsqueda
 *
 *  Parámetros: no tiene
 *  Devuelve: nada
 */
Vista.prototype.inicio = function () {
	
	// Descripción del padre
	this.setDescPadre();

	// Filtro
	this.añadirFiltro();

	// Botón de arriba
	this.setBotonArriba();

	// Llamada a buscar
	this.buscar(false);

}

/*
 * Método setDescPadre()
 *
 *  Añade la descripción del padre, si procede
 *
 *  Parámetros: no tiene
 *  Devuelve: nada
 */
Vista.prototype.setDescPadre = function () {

	if ( this.pantalla.descPadreRelacionada != "" ) {
		document.getElementById("txtDescPadre").innerHTML = "Estás en <b>" + this.pantalla.descPadreRelacionada + "</b>";
		document.getElementById("extDescPadre").style.display = "";
	}

}

/*
 * Método añadirFiltro()
 *
 *  Añade un filtro a la lista de filtros de la vista.
 *
 *  Parámetros: no tiene
 *  Devuelve: nada
 */
Vista.prototype.añadirFiltro = function () {

	var objCampo = null;
	var numFiltro = this.numFiltros++;
	var anchoFiltro = (this.esLista ? "31" : "59");
	var objFiltros = document.getElementById("filtros");
	var filtro = '<div id="filtro' + numFiltro + '" style="display: block;">';
	
	// Combo para seleccionar campo
	var selected = "";
	filtro += '<select id="selCampo' + numFiltro + '">';
	filtro += '<option value="">Selecciona un campo...</option>';
	for ( var k in this.pantalla.camposResultado ) {
		objCampo = this.pantalla.camposResultado[k];
		selected = (objCampo.selected == "S" ? 'selected="selected"' : "");
		filtro += '<option value="' + k + '" ' + selected + '>' + objCampo.descripcion + '</option>';
	}
	filtro += '</select>&nbsp;';
	// Caja de texto de búsqueda
	filtro += '<input type="text" class="inputTexto" id="inputCampo' + numFiltro + '" size="' + anchoFiltro + '" onkeydown="javascript: if ( event.keyCode == 13 ) { vista.buscar(true); };" />&nbsp;';
	filtro += '<input type="button" id="btnBuscar" class="boton" value="Buscar" style="width: 100px;" title="Seleccione un campo y busque en esta pantalla" onmouseover="javascript: if ( !this.readOnly ) { this.className=\'botonOver\'; };" onmouseout="javascript: this.className=\'boton\';" onclick="javascript: vista.buscar(true);" />';
	filtro += '</div>';

	// Asignación y visualización del filtro
	objFiltros.innerHTML += filtro;
	document.getElementById("extFiltro").style.display = "";

	// Usabilidad: selección de campo de filtro y foco en la búsqueda
	var objSelect = document.getElementById("selCampo" + numFiltro);
	if ( objSelect.selectedIndex == 0 )  objSelect.selectedIndex = 1;
	document.getElementById("inputCampo" + numFiltro).focus();

}

/*
 * Método setBotonArriba()
 *
 *  Inicializa el botón disponible en la parte superior de la pantalla
 *
 *  Parámetros: no tiene
 *  Devuelve: nada
 */
Vista.prototype.setBotonArriba = function () {

	var objBoton = document.getElementById("btnArriba");

	if ( this.pantalla.lista == "S" ) {
		objBoton.value = "Cerrar";
		objBoton.title = "Cierra esta ventana";
		objBoton.disabled = false;
	}
	else {
		objBoton.value = "Ir Atrás";
		objBoton.title = "Ir a la pantalla anterior";
		objBoton.disabled = false;
	}

	objBoton.style.display = "";

}

/*
 * Método accionBotonArriba()
 *
 *  Realiza la acción asociada a pulsar el botón disponible en la parte superior de la pantalla
 *
 *  Parámetros: no tiene
 *  Devuelve: nada
 */
Vista.prototype.accionBotonArriba = function () {

	if ( this.pantalla.lista == "S" )   window.close();
	else  history.back();

}

/*
 * Método buscar()
 *
 *  Realiza la llamada al servidor para buscar resultados.
 *
 *  Parámetros: bReset: 'true' indica que se construya de nuevo la condición de búsqueda y se reinicien
 *                      contadores.
 *  Devuelve: nada
 */
Vista.prototype.buscar = function (bReset) {

	try {
		// Condición de búsqueda
		var consulta = this.pantalla.selectResultado;
		if ( bReset ) {
			this.filaInicial = 0;
			//this.condicionBusqueda = this.getCondicionBusqueda();
			this.setCondicionBusqueda();
		}
		if ( this.condicionBusqueda != "" ) {
			if ( consulta.toUpperCase().indexOf(" WHERE ") > -1 )  consulta += " AND ";
			else  consulta += " WHERE ";
			consulta += this.condicionBusqueda;
		}
		consulta += " ORDER BY " + this.pantalla.campoOrden + " " + this.pantalla.tipoOrden;
		// Objeto para llamada asíncrona
		var objAjax = new AJAXRequest();
		objAjax.setUrl("servidor.php");
		objAjax.addParametro("tipoServidor", "Consulta");
		objAjax.addParametro("numAcciones", "1");
		objAjax.addParametro("sql", consulta);
		//alert(consulta);
		objAjax.addParametro("plantilla", this.plantilla);
		objAjax.addParametro("numFilas", this.numFilasPorPagina);
		objAjax.addParametro("filaInicial", this.filaInicial);
		// Información para log
		objAjax.addParametro("usuario", this.pantalla.usuario);
		objAjax.addParametro("libtabla", this.pantalla.libreriaTabla);
		var objThis = this;
		objAjax.setFuncionDeVuelta(function () { objThis.vueltaBuscar(objAjax); });
		objAjax.enviar();
		// Mensaje de búsqueda
		this.mostrarAccion("REALIZANDO BÚSQUEDA...");
	}
	catch (e) {
		alert(e.message);
	}

}

/*
 * Método vueltaBuscar()
 *
 *  Gestiona el resultado obtenido al realizar la búsqueda.
 *
 *  Parámetros: objAjax: objeto AJAXRequest para la petición de resultados.
 *  Devuelve: nada
 */
Vista.prototype.vueltaBuscar = function (objAjax) {

	try {
		if ( !objAjax.finCarga() )  return;
		if ( objAjax.hayErrorHTTP() )  throw new Error(objAjax.getErrorHTTP());

		//alert(objAjax.objHttpRequest.responseText);
		var objResultado = objAjax.getRespuestaJSON().resultado_consulta;
		//alert(objResultado.campos);
		
		// Control de error:
		if ( objResultado.error != "" )  throw new Error(objResultado.error);

		// Control de página vacía (tras borrar único elemento de última página)
		if ( (parseInt(objResultado.numFilas) == 0) && (parseInt(objResultado.numResultados) > 0) ) {
			this.filaInicial -= this.numFilasPorPagina;
			this.buscar(false);
		}
		else {
			// Resultado
			this.setResultado(objResultado);

			// Navegador
			this.setNavegador(objResultado);

			// Mostramos finalmente
			this.ocultarAccion(objResultado.numResultados == "0");
		}
	}
	catch (e) {
		alert(e.message);
	}

}

/*
 * Método setResultado()
 *
 *  Rellena el resultado con los datos obtenidos del servidor.
 *
 *  Parámetros: objResultado: objeto con los datos de la consulta realizada.
 *  Devuelve: nada
 */
Vista.prototype.setResultado = function (objResultado) {

	var resultado = '<table cellpadding="5" cellspacing="2" class="clTablaResultado" align="center">';
	resultado += this.cabecera;
	if ( objResultado.numResultados > 0 ) {
		//alert(objResultado.contenido);
		resultado += objResultado.contenido;
	}
	else {
		//resultado += '<tr><td></td><td colspan="100" align="center"><big>No se han encontrado resultados</big></td></tr>';
		if ( this.pantalla.camposResultado ) {
			colspan = this.pantalla.camposResultado.length;
			resultado += '<tr class="clFila"><td class="clTablaOut"></td><td colspan="' + colspan + '" align="center"><big>No se han encontrado resultados</big></td></tr>';
		}
		else {
			resultado += '<tr class="clFila"><td colspan="100" align="center"><big>No se han encontrado resultados</big></td></tr>';
		}
	}
	resultado += '</table>';

	// Mostramos el resultado
	document.getElementById("textoResultado").innerHTML = resultado;

	// Hacemos visible el orden
	var objCampo = null;
	if ( this.pantalla.camposResultado ) {
		// - primero quitamos todos
		for ( var k in this.pantalla.camposResultado ) {
			objCampo = this.pantalla.camposResultado[k];
			document.getElementById("icoOrden" + objCampo.nombre).innerHTML = "";
		}
		// - después añadimos el actual
		objIcono = document.getElementById("icoOrden" + this.pantalla.campoOrden);
		if ( objIcono ) {
			objIcono.innerHTML = '<font face="Webdings">' + (this.pantalla.tipoOrden == "ASC" ? '5' : '6') + '</font>';
		}
	}

}

/*
 * Método setNavegador()
 *
 *  Configura el navegador en función de los resultados obtenidos.
 *
 *  Parámetros: objResultado: objeto con los datos de la consulta realizada.
 *  Devuelve: nada
 */
Vista.prototype.setNavegador = function (objResultado) {

	// Datos del objeto
	this.filaInicial = parseInt(objResultado.filaInicial);
	this.numFilas = parseInt(objResultado.numFilas);
	this.numResultados = parseInt(objResultado.numResultados);

	// Contadores
	var numFilas = this.numFilas;
	var numResultados = this.numResultados;
	var filaInicial = this.filaInicial + 1;
	var filaFinal = numFilas + filaInicial - 1;

	if ( numResultados == 0 )  return;

	// Texto del navegador
	var txtNavegador = filaInicial + "-" + filaFinal + " de " + numResultados;
	txtNavegador += (numResultados == 1 ? " resultado" : " resultados");
	
	// Botones
	document.getElementById("btnInicio").disabled = (filaInicial == 1);
	document.getElementById("btnAnterior").disabled = (filaInicial == 1);
	document.getElementById("btnSiguiente").disabled = (filaFinal == numResultados);
	document.getElementById("btnFinal").disabled = (filaFinal == numResultados);

	// Mostramos el navegador
	document.getElementById("textoNavegador").innerHTML = txtNavegador + "&nbsp;&nbsp;";

}

/*
 * Método ordena()
 *
 *  Realiza la llamada de búsqueda cuando se hace una ordenación.
 *
 *  Parámetros: campo: campo por el que se realiza la ordenación.
 *  Devuelve: nada
 */
Vista.prototype.ordena = function (campo) {

	// Campo del campo y el tipo de orden
	if ( campo == this.pantalla.campoOrden ) {
		this.pantalla.tipoOrden = (this.pantalla.tipoOrden == "ASC" ? "DESC": "ASC");
	}
	else {
		this.pantalla.tipoOrden = "ASC";
	}
	this.pantalla.campoOrden = campo;

	// Llamada a la búsqueda
	this.buscar();

}

/*
 * Método ir()
 *
 *  Realiza la llamada de búsqueda cuando se pide una página en el navegador.
 *
 *  Parámetros: objBoton: botón del navegador pulsado.
 *              donde: 'I': página inicial; 'A': página anterior; 'S': página siguiente; 'F': última página
 *  Devuelve: nada
 */
Vista.prototype.ir = function (objBoton, donde) {

	// Control de permiso
	if ( objBoton.disabled )  return;

	// Estilo del botón por defecto
	objBoton.className = "boton";

	switch ( donde ) {
		case 'I': { this.filaInicial = 0; break; }
		case 'A': { this.filaInicial = this.filaInicial - this.numFilasPorPagina; break; }
		case 'S': { this.filaInicial = this.filaInicial + this.numFilasPorPagina; break; }
		case 'F': { 
			this.filaInicial = this.numResultados - ((this.numResultados - 1) % this.numFilasPorPagina) - 1;
			break;
		}
	}

	// Llamada a la búsqueda
	this.buscar(false);

}

/*
 * Método verAcciones()
 *
 *  Muestra el menú de acciones de la pantalla.
 *
 *  Parámetros: objAncla: objeto donde se pulsó para pedir el menú.
 *              clave: clave del registro seleccionadi
 *              camposRelacionadas: lista de campos de las pantallas relacionadas
 *              valoresRelacionadas: lista de valores de las pantallas relacionadas
 *              descPadreRelacionadas: lista de descripciones para las relacionadas
 *  Devuelve: nada
 */
Vista.prototype.verAcciones = function (objAncla) {

	var onmouses = 'onmouseover="javascript: this.className=\'clMenuEnabledActive\';" onmouseout="javascript: this.className=\'clMenuEnabled\';"';

	// Opciones generales
	var contenidoMenu = '<div class="clMenu">';
	contenidoMenu += '<div class="clMenuCab" onclick="javascrip: vista.menu.ocultar();" align="center">ACCIONES</div>';
	if ( (this.pantalla.libreriaTabla != "" ) && (this.pantalla.permisoModificar == "S" ) ) {
		contenidoMenu += '<div class="clMenuEnabled" onclick="javascript: vista.editar(\'\');" ' + onmouses + '>Nuevo</div>';
	}
	var bEnabled = (this.numResultados > 0);
	var enabled = (bEnabled ? "Enabled" : "Disabled");
	contenidoMenu += '<div class="clMenu' + enabled + '" ' + (bEnabled ? ('onclick="javascript: vista.generarInforme(\'PDF\');" ' + onmouses) : "") + '>Generar PDF</div>';
	contenidoMenu += '<div class="clMenu' + enabled + '" '+ (bEnabled ? ('onclick="javascript: vista.generarInforme(\'Excel\');" ' + onmouses) : "") + '>Generar Excel</div>';
	contenidoMenu += '</div>';

	try {
		this.menu.objetoAncla = objAncla;
		this.menu.contenido = contenidoMenu;
		this.menu.mostrar();
	}
	catch (e) {
		alert(e.message);
	}

}

/*
 * Método verMenu()
 *
 *  Muestra el menú contextual de un registro dado.
 *
 *  Parámetros: objAncla: objeto donde se pulsó para pedir el menú.
 *              numFila: número de la fila seleccionada
 *  Devuelve: nada
 */
Vista.prototype.verMenu = function (objAncla, numFila) {

	var onmouses = 'onmouseover="javascript: this.className=\'clMenuEnabledActive\';" onmouseout="javascript: this.className=\'clMenuEnabled\';"';

	// Opciones generales
	var enabled = (this.pantalla.permisoModificar == "S" ? "Enabled" : "Disabled");
	var contenidoMenu = '<div class="clMenu">';
	// - Título del div
	contenidoMenu += '<div class="clMenuCab" onclick="javascrip: vista.menu.ocultar();" align="center">ACCIONES</div>';
	// - Opciones de mantenimiento
	if ( this.pantalla.libreriaTabla != "" ) {
		// - Editar/ver
		contenidoMenu += '<div class="clMenuEnabled" onclick="javascript: vista.editar(\'' + numFila + '\');" ' + onmouses + '>' + (this.pantalla.permisoModificar == "S" ? "Editar" : "Detalle") + '</div>';
		// - Eliminar
		if ( this.pantalla.permisoModificar == "S" )  contenidoMenu += '<div class="clMenu' + enabled + '" onclick="javascript: vista.eliminar(\'' + numFila + '\');" ' + onmouses + '>Eliminar</div>';
	}
	// Relacionadas
	if ( this.pantalla.relacionadas ) {
		var objRelacionada = null;
		for ( var k in this.pantalla.relacionadas ) {
			objRelacionada = this.pantalla.relacionadas[k];
			if ( objRelacionada.permiso != "S" )  continue;
			contenidoMenu += '<div class="clMenuEnabled" onclick="javascript: vista.irARelacionada(\'' + numFila + '\', \'' + k + '\');" ' + onmouses + '>Ver ' + objRelacionada.descripcion.replace(/_/g," ").htmlEncode() + '</div>';
		}
		contenidoMenu += '</div>';
	}

	try {
		this.menu.objetoAncla = objAncla;
		this.menu.contenido = contenidoMenu;
		this.menu.mostrar();
	}
	catch (e) {
		alert(e.message);
	}

}

/*
 * Método generarInforme()
 *
 *  Genera un informe con los resultados de la búsqueda
 *
 *  Parámetros: tipo: 'PDF' o 'Excel'
 *  Devuelve: nada
 */
Vista.prototype.generarInforme = function (tipo) {

	// Ocultamos el menú
	this.menu.ocultar();

	try {
		// Variables extra (para cada caso)
		var condicion = this.condicionBusqueda;
		var orderBy = this.pantalla.campoOrden + " " + this.pantalla.tipoOrden;
		var campoOrden = "";
		for ( var k in this.pantalla.camposResultado ) {
			if ( this.pantalla.camposResultado[k].nombre == this.pantalla.campoOrden ) {
				campoOrden = this.pantalla.camposResultado[k].descripcion;
				break;
			}
		}
		var tipoOrden = (this.pantalla.tipoOrden == "ASC" ? "ascendente" : "descendente");
		var descripcion = this.textoCondicionBusqueda;
		if ( descripcion != "" )  descripcion += "\n";
		descripcion += "Ordenado por " + campoOrden + " " + tipoOrden;

		// Lanza el informe (por ahor, PDF)
		var url = "informe.php?fuente=pantalla&informe=" + this.pantalla.nombre;
		url += "&tipo=" + tipo;
		url += "&condicion=" + condicion.codificar();
		url += "&descripcion=" + descripcion.codificar();
		url += "&orden=" + orderBy.codificar();
		url += "&rand=" + new Date().getMilliseconds();
		var ventana = new Ventana(url, 950, 700, true);
		if ( !ventana.mostrar() )  throw new Error("Tiene activado el bloqueador de ventanas emergentes. Por favor, desactívelo para este sitio web");
	}
	catch (e) {
		alert(e.message);
	}
	

}

/*
 * Método editar()
 *
 *  Abre la ventana para editar/ver un registro concreto.
 *
 *  Parámetros: numFila: número de la fila del registro a editar, o bien vacío si es nuevo
 *  Devuelve: nada
 */
Vista.prototype.editar = function (numFila) {

	this.menu.ocultar();

	// Clave
	var objClave = document.getElementById("clave_" + numFila);
	var clave = (objClave ? objClave.value : "");

	// Dirección de la edición
	var url = "edicion.php?pantalla=" + this.pantalla.nombre;
	url += "&usuario=" + this.pantalla.usuario;
	url += "&clave=" + clave.codificar();
	url += "&camposFijos=" + this.pantalla.camposFijos.codificar();
	url += "&valoresFijos=" + this.pantalla.valoresFijos.codificar();

	// Ventana emergente
	var ventana = new Ventana(url, 820, 600, true, "edicion");
	ventana.scrollbars = true;
	try {
		if ( !ventana.mostrar() )  throw new Error("Tiene activado el bloqueador de ventanas emergentes. Por favor, desactívelo para este sitio web");
	}
	catch (e) {
		alert(e.message);
	}

}

/*
 * Método eliminar()
 *
 *  Realiza la llamada al servidor para eliminar un registro.
 *
 *  Parámetros: numFila: número de la fila a eliminar.
 *  Devuelve: nada
 */
Vista.prototype.eliminar = function (numFila) {

	// Control de permisos
	if ( this.pantalla.permisoModificar != "S" )  return;

	// Pregunta de confirmación
	this.menu.ocultar();
	if ( !confirm("¿Desea eliminar el registro?") )  return;

	// Clave
	var clave = document.getElementById("clave_" + numFila).value;

	// Llamada al servidor
	try {
		// Objeto para llamada asíncrona
		var objAjax = new AJAXRequest();
		objAjax.setUrl("servidor.php");
		objAjax.addParametro("usuario", this.pantalla.usuario);
		objAjax.addParametro("tipoServidor", "Ejecuta");
		objAjax.addParametro("numAcciones", "1");
		objAjax.addParametro("tipoOperacion1", "DELETE");
		objAjax.addParametro("libtabla1", this.pantalla.libreriaTabla);
		objAjax.addParametro("condicionDelete1", clave);
		var objThis = this;
		objAjax.setFuncionDeVuelta(function () { objThis.vueltaEliminar(objAjax); });
		objAjax.enviar();
		// Mensaje
		//this.mostrarAccion("REALIZANDO BÚSQUEDA...");
	}
	catch (e) {
		alert(e.message);
	}

}

/*
 * Método vueltaEliminar()
 *
 *  Comprueba el resultado de la eliminación y realiza las operaciones posteriores oportunas.
 *
 *  Parámetros: objAjax: objeto AJAXRequest que realizó la llamada.
 *  Devuelve: nada
 */
Vista.prototype.vueltaEliminar = function (objAjax) {

	try {
		if ( !objAjax.finCarga() )  return;
		if ( objAjax.hayErrorHTTP() )  throw new Error(objAjax.getErrorHTTP());

		//alert(objAjax.objHttpRequest.responseText);
		var objResultado = objAjax.getRespuestaJSON().resultado_consulta;
		
		// Control de error
		var error = objResultado.operacion[0].error;
		if ( error != "" )  throw new Error(error);

		// Se ha borrado con éxito: recargamos los resultados
		this.buscar(false);
	}
	catch (e) {
		alert(e.message);
	}

}

/*
 * Método irARelacionada()
 *
 *  Cambia la localización de esta ventana  la de la relacionada indicada.
 *
 *  Parámetros: numFila: número de fila del registro seleccionado
 *              numRelacionada: número de pantalla relacionada a la que se quiere ir
 *  Devuelve: nada
 */
Vista.prototype.irARelacionada = function (numFila, numRelacionada) {

	var nombre = this.pantalla.relacionadas[numRelacionada].nombre;
	var clave = document.getElementById("clave_" + numFila).value;
	var camposRelacionada = document.getElementById("camposRelacionadas_" + numFila).value.split("|")[numRelacionada];
	var valoresRelacionada = document.getElementById("valoresRelacionadas_" + numFila).value.split("|")[numRelacionada];
	var descPadreRelacionada = document.getElementById("descPadreRelacionadas_" + numFila).value.split("|")[numRelacionada];

	this.menu.ocultar();
	//alert(nombre + ": " + clave + ", " + camposRelacionada + ", " + valoresRelacionada);
	var url = "vista.php?pantalla=" + nombre;
	url += "&usuario=" + this.pantalla.usuario;
	url += "&condicionFija=" + clave.codificar();
	url += "&camposFijos=" + camposRelacionada.codificar();
	url += "&valoresFijos=" + valoresRelacionada.codificar();
	url += "&descPadreRelacionada=" + descPadreRelacionada.codificar();

	window.location = url;

}

/*
 * Método volverLista()
 *
 *  Realiza las acciones asociadas a la vuelta de la lista que se abrió previamente.
 *
 *  Parámetros: numFila: número de la fila de la que se recuperan los valores
 *  Devuelve: nada
 */
Vista.prototype.volverLista = function (numFila) {

	var campos = document.getElementById("camposLista_" + numFila).value;
	var valores = document.getElementById("valoresLista_" + numFila).value;

	// Llamada a la ventana padre
	if ( window.opener && window.opener.edicion ) {
		window.opener.edicion.volverLista(campos.split("|"), valores.split("|"));
	}

	// Cierre retardado de esta ventana
	window.setTimeout("window.close()", 500);

}

/*
 * Método setCondicionBusqueda()
 *
 *  Obtiene la condición de búsqueda en el momento actual.
 *
 *  Parámetros: no tiene
 *  Devuelve: condición de búsqueda en el momento actual
 */
Vista.prototype.setCondicionBusqueda = function () {

	var arrayCondicion = new Array();
	var arrayTextoCondicion = new Array();
	if ( this.pantalla.condicionFija != "" )  arrayCondicion.push(this.pantalla.condicionFija);
	if ( this.pantalla.descPadreRelacionada != "" )  arrayTextoCondicion.push(this.pantalla.descPadreRelacionada);

	var objSelect, objCampo, objValor;
	var condicion, textoCondicion;
	for ( var k = 0; k < this.numFiltros; k++ ) {
		if ( document.getElementById("filtro" + k).style.display == "none" )  continue;
		objSelect = document.getElementById("selCampo" + k);
		objValor = document.getElementById("inputCampo" + k);
		if ( (objSelect.value == "") && objValor.value != "" )  throw new Error("Seleccione un campo para buscar");
		if ( (objSelect.value == "") && objValor.value == "" )  continue;

		objCampo = this.pantalla.camposResultado[parseInt(objSelect.value)];
		condicion = objCampo.nombre;
		textoCondicion = objCampo.descripcion;
		if ( objCampo.tipo == "D" ) {
			var valor = objValor.value.replace(/\./g, "");
			valor = valor.replace(/,/g, ".");
			if ( (valor == "") || isNaN(valor) )  throw new Error("Texto a buscar no válido (se espera un número)");
			condicion += " = " + valor;
			textoCondicion += " vale " + objValor.value;
		}
		else if ( objCampo.tipo == "F" ) {
			var fecha = new Fecha(objValor.value);
			condicion += " = '" + fecha.getFechaSQL() + "'";
			textoCondicion += " vale " + fecha.getFechaISO();
		}
		else {
			condicion += " LIKE '%" + objValor.value.replace(/\'/g, "''") + "%'";
			textoCondicion += " contiene '" + objValor.value + "'";
		}
		arrayCondicion.push(condicion);
		arrayTextoCondicion.push(textoCondicion);
	}

	//alert(arrayCondicion.join(" AND "));
	//return arrayCondicion.join(" AND ");
	this.condicionBusqueda = arrayCondicion.join(" AND ");
	this.textoCondicionBusqueda = arrayTextoCondicion.join(" y ");

}

/*
 * Método mostrarAccion()
 *
 *  Muestra el mensaje de acción indicado.
 *
 *  Parámetros: msgAccion: mensaje de acción a mostrar.
 *  Devuelve: nada
 */
Vista.prototype.mostrarAccion = function (msgAccion) {
//return;

	/*
	document.getElementById("extResultado").style.display = "none";
	document.getElementById("extNavegador").style.display = "none";

	document.getElementById("textoAccion").innerHTML = msgAccion.htmlEncode();
	document.getElementById("extMsgAccion").style.display = "";
	*/

}

/*
 * Método ocultarAccion()
 *
 *  Oculta el mensaje de acción
 *
 *  Parámetros: bOcultarNavegador: 'true' indica que se debe ocultar el navegador.
 *  Devuelve: nada
 */
Vista.prototype.ocultarAccion = function (bOcultarNavegador) {

	document.getElementById("extMsgAccion").style.display = "none";
	document.getElementById("extResultado").style.display = "";
	document.getElementById("extNavegador").style.display = (bOcultarNavegador ? "none" : "");
	
}