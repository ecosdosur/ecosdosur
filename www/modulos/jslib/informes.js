/*
 *  Funcionalidades específicas de la pantalla de informes
 *
 *  Autor: Pentared
 *  Última actualización: 23/02/2007
 */

// Control de código: ha de estar incluido el fichero 'Vista.js'
if ( typeof Vista != "undefined" ) {


/*
 * Método inicio()
 *
 *  Realiza las operaciones pertinentes tras la carga de la página. Sobrepone al que hay en 'Vista.js'
 *
 *  Parámetros: no tiene
 *  Devuelve: nada
 */
Vista.prototype.inicio = function () {

	// Métodos de la clase base
	this.setBotonArriba();

	// Array con objetos relacionados
	this.arrayRelacionados = new Array();

	// Se realiza la llamada para obtener los distintos tipos de informes
	this.buscarInformes();

}

/*
 * Método buscarInformes()
 *
 *  Realiza la llamada para buscar la parametrización de los informes existentes.
 *
 *  Parámetros: no tiene
 *  Devuelve: nada
 */
Vista.prototype.buscarInformes = function () {

	try {
		var consulta = this.pantalla.selectResultado;
		var objAjax = new AJAXRequest();
		objAjax.setUrl("servidor.php");
		objAjax.addParametro("tipoServidor", "Consulta");
		objAjax.addParametro("numAcciones", "1");
		objAjax.addParametro("sql", consulta);
		//alert(consulta);
		//objAjax.addParametro("plantilla", this.plantilla);
		//objAjax.addParametro("numFilas", 1);
		//objAjax.addParametro("filaInicial", 0);
		// Información para log
		objAjax.addParametro("usuario", this.pantalla.usuario);
		objAjax.addParametro("libtabla", 'informes');
		var objThis = this;
		objAjax.setFuncionDeVuelta(function () { objThis.vueltaBuscarInformes(objAjax); });
		objAjax.enviar();
		// Mensaje de búsqueda
		this.mostrarAccion("CARGANDO INFORMES...");
	}
	catch (e) {
		alert(e.message);
	}


}

/*
 * Método vueltaBuscarInformes()
 *
 *  Recupera la parametrización de los informes existentes y pinta
 *   en pantalla la selección de los mismos.
 *
 *  Parámetros: objAjax: objeto AJAXRequest con el resultado de la consulta
 *  Devuelve: nada
 */
Vista.prototype.vueltaBuscarInformes = function (objAjax) {

	try {
		if ( !objAjax.finCarga() )  return;
		if ( objAjax.hayErrorHTTP() )  throw new Error(objAjax.getErrorHTTP());

		//alert(objAjax.objHttpRequest.responseText);
		this.objInformes = objAjax.getRespuestaJSON().resultado_consulta;
		//alert(objResultado.campos);
		
		// Control de error:
		if ( this.objInformes.error != "" )  throw new Error(this.objInformes.error);

		// Pintamos el HTML para visualizar los informes
		this.pintaSeleccionInformes();

	}
	catch (e) {
		alert(e.message);
		this.ocultarAccion(true);
	}


}

/*
 * Método pintaSeleccionInformes()
 *
 *  Pinta en pantalla un combo con los posibles informes disponibles.
 *
 *  Parámetros: no tiene
 *  Devuelve: nada
 */
Vista.prototype.pintaSeleccionInformes = function () {

	// Sustituimos el filtro por la selección de los informes
	var objRegistro = null;
	var textoFiltro = '<br />Seleccione un informe:&nbsp;';
	textoFiltro += '<select id="selInforme" onchange="javascript: vista.buscarRelacionados();"><option value="">Escoja un informe...</option>';
	for ( var k in this.objInformes.registro ) {
		objRegistro = this.objInformes.registro[k];
		textoFiltro += '<option value="' + objRegistro.CODINFORME + '">' + objRegistro.DESCRIPCION + '</option>';
	}
	textoFiltro += '<option value="FICHERO">Fichero de log</option>';
	textoFiltro += '</select>&nbsp;&nbsp;&nbsp;';
	textoFiltro += '<input type="radio" id="tipoInformePDF" onclick="javascript: this.checked = true; document.getElementById(\'tipoInformeEXCEL\').checked = false;" />PDF&nbsp;<input type="radio" id="tipoInformeEXCEL" onclick="javascript: this.checked = true; document.getElementById(\'tipoInformePDF\').checked = false;" />Excel&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	textoFiltro += '<input type="button" id="btnLanzar" value="Generar" class="boton" onmouseover="javascript: if ( !this.disabled ) { this.className=\'botonOver\'; };" onmouseout="javascript: this.className=\'boton\';" onclick="javascript: vista.lanzarInforme();" style="width: 150px;"/>';
	textoFiltro += '<p />';
	textoFiltro += '<div id="extOpcionesInforme" style="display: none;"><div id="inOpcionesInforme" class="clMsgEncuadrado"></div></div>';

	document.getElementById("inFiltro").innerHTML = textoFiltro;
	document.getElementById("extFiltro").style.display = "";
	this.ocultarAccion(true);

}

/*
 * Método buscarRelacionados()
 *
 *  Busca los objetos relacionados del informe seleccionado.
 *
 *  Parámetros: no tiene
 *  Devuelve: nada
 */
Vista.prototype.buscarRelacionados = function () {

	// Aviso de carga
	var objExtOpcionesInforme = document.getElementById("extOpcionesInforme");
	var objInOpcionesInforme = document.getElementById("inOpcionesInforme");
	objExtOpcionesInforme.style.display = "";
	objInOpcionesInforme.innerHTML = '<div style="text-align: center;"><big><b><i>CARGANDO FILTRO...</i></b></big></div>';

	var objSelect = document.getElementById("selInforme");

	try {
		// Protección contra selección no válida
		if ( objSelect.value == "" )  return;

		if ( objSelect.value == "FICHERO" ) {
			this.preLanzarInforme();
		}
		else {
			camposFil = this.objInformes.registro[objSelect.selectedIndex - 1].CAMPOSFIL;
			if ( camposFil != "" ) {
				var arrayCamposFil = camposFil.split(" ");
				for ( var k in arrayCamposFil ) {
					arrayCampoFil = arrayCamposFil[k].split("|");
					if ( arrayCampoFil.length < 3 )  throw new Error("Error en la parametrización del informe: " + this.objInformes.registro[objSelect.selectedIndex - 1].DESCRIPCION);
					tipo = arrayCampoFil[2];
					arrayTipo = tipo.split("_");
					if ( arrayTipo.length == 1 )  continue;
					relacionado = arrayTipo[1];
					objRelacionado = this.arrayRelacionados[relacionado];
					if ( !objRelacionado ) {
						this.buscarRelacionado(relacionado);
						return;
					}
				}
			}
			// Están cargados todos los relacionados
			this.preLanzarInforme();
		}
	}
	catch (e) {
		alert(e.message);
	}

}

/*
 * Método buscarRelacionado()
 *
 *  Busca la consulta que se ha de realizar para obtener la lista de posibles valores de un campo
 *   relacionado.
 *
 *  Parámetros: relacionado: nombre de la pantalla del campo relacionado
 *  Devuelve: nada
 */
Vista.prototype.buscarRelacionado = function (relacionado) {

	try {
		var consulta = "SELECT SELECTRES, CAMPOSRES FROM " + libreriaSistema + ".pantallas WHERE PANTALLA='" + relacionado + "'";
		var objAjax = new AJAXRequest();
		objAjax.setUrl("servidor.php");
		objAjax.addParametro("tipoServidor", "Consulta");
		objAjax.addParametro("numAcciones", "1");
		objAjax.addParametro("sql", consulta);
		var objThis = this;
		objAjax.setFuncionDeVuelta(function () { objThis.vueltaBuscarRelacionado(objAjax, relacionado); });
		objAjax.enviar();
		// Mensaje de búsqueda
		//this.mostrarAccion("CARGANDO INFORMES...");
	}
	catch (e) {
		alert(e.message);
	}


}

/*
 * Método vueltaBuscarRelacionado()
 *
 *  Comprueba que se ha recuperado correctamente la consulta y realiza la llamada
 *   para obtener la lista de valores
 *
 *  Parámetros: objAjax: objeto de la llamada asíncrona
 *              relacionado: nombre de la pantalla del campo relacionado
 *  Devuelve: nada
 */
Vista.prototype.vueltaBuscarRelacionado = function (objAjax, relacionado) {

	try {
		if ( !objAjax.finCarga() )  return;
		if ( objAjax.hayErrorHTTP() )  throw new Error(objAjax.getErrorHTTP());

		//alert(objAjax.objHttpRequest.responseText);
		objResultado = objAjax.getRespuestaJSON().resultado_consulta;
		//alert(objResultado.campos);
		
		// Control de error:
		if ( objResultado.error != "" )  throw new Error(objResultado.error);
		if ( objResultado.registro.length == 0 )  throw new Error("No existe el campo relacionado " + relacionado);

		// Consulta
		var consulta = objResultado.registro[0].SELECTRES;
		var campoOrden = "";
		var arrayCamposRes = objResultado.registro[0].CAMPOSRES.split(" ");
		for ( var k in arrayCamposRes ) {
			arrayCampoRes = arrayCamposRes[k].split("|");
			if ( arrayCampoRes.length < 4 )  throw new Error("Parametrización de pantalla incorrecta: " + relacionado);
			var tipo = arrayCampoRes[3];
			if ( tipo.indexOf("#") > -1 ) {
				campoOrden = arrayCampoRes[1];
				break;
			}
			if ( campoOrden == "" )  campoOrden = arrayCampoRes[1];
		}
		consulta += " ORDER BY " + campoOrden;
		this.cargarRelacionado(relacionado, consulta);
	}
	catch (e) {
		alert(e.message);
	}

}

/*
 * Método cargarRelacionado()
 *
 *  Realiza la llamada para cargar los posibles valores del campo relacionado
 *
 *  Parámetros: relacionado: nombre de la pantalla del campo relacionado
 *              consulta: consulta a realizar para obtener los valores posibles del campo relacionado
 *  Devuelve: nada
 */
Vista.prototype.cargarRelacionado = function (relacionado, consulta) {

	try {
		var objAjax = new AJAXRequest();
		objAjax.setUrl("servidor.php");
		objAjax.addParametro("tipoServidor", "Consulta");
		objAjax.addParametro("numAcciones", "1");
		objAjax.addParametro("sql", consulta);
		var objThis = this;
		objAjax.setFuncionDeVuelta(function () { objThis.vueltaCargarRelacionado(objAjax, relacionado); });
		objAjax.enviar();
		// Mensaje de búsqueda
		//this.mostrarAccion("CARGANDO INFORMES...");
	}
	catch (e) {
		alert(e.message);
	}


}

/*
 * Método vueltaCargarRelacionado()
 *
 *  Comprueba que se ha recuperado correctamente la lista de valores del campo relacionado
 *   y continúa con la carga de los posibles siguientes
 * 
 *  Parámetros: objAjax: objeto de la llama asíncrona
 *              relacionado: nombre de la pantalla del campo relacionado
 *  Devuelve: nada
 */
Vista.prototype.vueltaCargarRelacionado = function (objAjax, relacionado) {

	try {
		if ( !objAjax.finCarga() )  return;
		if ( objAjax.hayErrorHTTP() )  throw new Error(objAjax.getErrorHTTP());

		//alert(objAjax.objHttpRequest.responseText);
		objResultado = objAjax.getRespuestaJSON().resultado_consulta;
		//alert(objResultado.campos);
		
		// Control de error:
		if ( objResultado.error != "" )  throw new Error(objResultado.error);

		// Éxito: pasamos a los posibles siguientes relacionados
		this.arrayRelacionados[relacionado] = objResultado;
		this.buscarRelacionados();

	}
	catch (e) {
		alert(e.message);
	}

}


/*
 * Método preLanzarInforme()
 *
 *  Realiza las acciones cuando se selecciona un informe en el combo de informes: muestra los
 *   posibles campos para ordenación y, en caso de tener, los campos para filtrar.
 *
 *  Parámetros: no tiene
 *  Devuelve: nada
 */
Vista.prototype.preLanzarInforme = function () {

	var objSelect = document.getElementById("selInforme");
	var objExtOpcionesInforme = document.getElementById("extOpcionesInforme");
	var objInOpcionesInforme = document.getElementById("inOpcionesInforme");
	var textoOpcionesInforme = "";

	// Faltan los informes con filtro
	if ( objSelect.value == "FICHERO" ) {
		textoOpcionesInforme = "Fecha del fichero de log:&nbsp;&nbsp;&nbsp;";
		textoOpcionesInforme += '<input type="text" id="fechaLog" size="12" maxlength="10" class="inputTextoObligatorio" style="text-align: center" /><img src="img/calendario20.gif" style="cursor: pointer;" onclick="return showCalendar(\'fechaLog\', \'%d/%m/%Y\');"/>';
	}
	else {
		// Ordenar por...
		textoOpcionesInforme = "<b>Ordenar por...</b>&nbsp;&nbsp;";
		textoOpcionesInforme += '<select id="selOrdenar">';
		var arrayCamposRes = this.objInformes.registro[objSelect.selectedIndex - 1].CAMPOSRES.split(" ");
		var arrayCampo = null;
		for ( var k in arrayCamposRes ) {
			arrayCampo = arrayCamposRes[k].split("|");
			nombre = arrayCampo[0];
			descripcion = arrayCampo[1].replace(/_/g, " ");
			textoOpcionesInforme += '<option value="' + nombre + '">' + descripcion + '</option>';
		}
		textoOpcionesInforme += '</select>&nbsp;';
		textoOpcionesInforme += '<select id="selOrdenarTipo">';
		textoOpcionesInforme += '<option value="ASC" selected="selected">ascendente</option>';
		textoOpcionesInforme += '<option value="DESC">descendente</option>';
		textoOpcionesInforme += '</select>';
		// Filtrar por...
		var camposFiltro = this.objInformes.registro[objSelect.selectedIndex - 1].CAMPOSFIL;
		if ( camposFiltro != "" ) {
			textoOpcionesInforme += '<p /><b>Filtrar por...</b>';
			textoOpcionesInforme += '<table style="font-size: 12px;" cellpadding="3">';
			var arrayCamposFiltro = camposFiltro.split(" ");
			for ( var k in arrayCamposFiltro ) {
				textoOpcionesInforme += '<tr>';
				arrayCampoFiltro = arrayCamposFiltro[k].split("|");
				nombre = arrayCampoFiltro[0];
				descripcion = arrayCampoFiltro[1].replace(/_/g, " ");
				tipo = arrayCampoFiltro[2];
				textoOpcionesInforme += '<td align="right"><i>' + descripcion + '</i> ';
				switch ( tipo ) {
					case "D": { operador = "entre"; break; }
					case "F": { operador = "desde"; break; }
					case "A" : { operador = "contiene"; break; }
					//case "C", "S", "G", "D" :
					default: { operador = "es"; break; }
				}
				textoOpcionesInforme += operador + ' </td>';
				//textoOpcionesInforme += '<td><input type="texto" class="inputTexto" /></td>';
				switch ( tipo ) {
					case "F": {
						nombreCampo = "campoFiltro" + k + "_desde";
						input = "<input type='text' id='" + nombreCampo + "' size='12' maxlength='10' class='inputTexto' style='text-align: center' /><img src='img/calendario20.gif' style='cursor: pointer;' onclick=\"return showCalendar('" + nombreCampo + "', '%d/%m/%Y');\"/>";
						input += " hasta ";
						nombreCampo = "campoFiltro" + k + "_hasta";
						input += "<input type='text' id='" + nombreCampo + "' size='12' maxlength='10' class='inputTexto' style='text-align: center' /><img src='img/calendario20.gif' style='cursor: pointer;' onclick=\"return showCalendar('" + nombreCampo + "', '%d/%m/%Y');\"/>";
						break;
					}
					case "C":
						case "S": {
						nombreCampo = "campoFiltro" + k + "_S";
						input = "<input type='checkbox' id='" + nombreCampo + "' /> Sí ";
						nombreCampo = "campoFiltro" + k + "_N";
						input += "<input type='checkbox' id='" + nombreCampo + "' /> No ";
						break;
					}
					case "G": {
						nombreCampo = "campoFiltro" + k + "_H";
						input = "<input type='checkbox' id='" + nombreCampo + "' /> Hombre ";
						nombreCampo = "campoFiltro" + k + "_M";
						input += "<input type='checkbox' id='" + nombreCampo + "' /> Mujer ";
						break;
					}
					case "D": {
						nombreCampo = "campoFiltro" + k + "_desde";
						input = "<input type='text' id='" + nombreCampo + "' size='10' class='inputTexto' />";
						input += " y ";
						nombreCampo = "campoFiltro" + k + "_hasta";
						input += "<input type='text' id='" + nombreCampo + "' size='10' class='inputTexto' />";
						break;
					}
					case "A": {
						nombreCampo = "campoFiltro" + k;
						input = "<input type='text' id='" + nombreCampo + "' size='35' class='inputTexto' />";
						break;
					}
					default: {
						nombreCampo = "campoFiltro" + k;
						// Campo relacionado
						arrayTipo = tipo.split("_");
						if ( arrayTipo.length == 1 )  throw new Error("Parametrización de informe no válida (tipo): " + this.objInformes.registro[objSelect.selectedIndex - 1].DESCRIPCION);
						relacionado = arrayTipo[1];
						input = "<select id='" + nombreCampo + "'>" + this.generarHtmlRelacionado(nombre, relacionado) + "</select>";
					}
				}
				textoOpcionesInforme += '<td>' + input + '</td>';
				textoOpcionesInforme += '</tr>';
			}
			textoOpcionesInforme += '</table>';
		}
	}

	// Volcamos el código HTML
	objInOpcionesInforme.innerHTML = '<big><b>OPCIONES</b></big><p />' + textoOpcionesInforme; // + '<br />';
	//objExtOpcionesInforme.style.display = "";

}

/*
 * Método generarHtmlRelacionado()
 *
 *  Genera el HTML interno del <select> correspondiente al campo relacionado.
 *
 *  Parámetros: nombreCampo: nombre del campo (generalmente tabla.campo) relacionado
 *              relacionado: código de la pantalla del campo relacionado.
 *  Devuelve: el código HTML
 */
Vista.prototype.generarHtmlRelacionado = function (nombreCampo, relacionado) {

	var objRelacionado = this.arrayRelacionados[relacionado];
	if ( !objRelacionado )  throw new Error("No se han cargado los valores para " + relacionado);
	var textoHtml = "<option value=''></option>";
	nombreCampo = nombreCampo.split(".");
	nombreCampo = nombreCampo[nombreCampo.length - 1];
	
	var valor, descripcion;
	for ( var k in objRelacionado.registro ) {
		valor = eval("objRelacionado.registro[" + k + "]." + nombreCampo);
		descripcion = objRelacionado.registro[k].DESCRIPCION;
		textoHtml += "<option value='" + valor + "'>" + descripcion + "</option>";
	}
	
	return textoHtml;

}

/*
 * Método lanzarInforme()
 *
 *  Realiza las acciones asociadas al botón de Generar un informe: comprobación de datos válidos 
 *   y lanzamiento de la ventana con el informe solicitado.
 *
 *  Parámetros: no tiene
 *  Devuelve: nada
 */
Vista.prototype.lanzarInforme = function () {

	try {
		// Control de informe seleccionado
		var objSelect = document.getElementById("selInforme");
		if ( objSelect.value == "" )  throw new Error('Por favor, seleccione un informe');

		// Control de tipo de informe
		var objTipoPDF = document.getElementById("tipoInformePDF");
		var objTipoExcel = document.getElementById("tipoInformeEXCEL");
		if ( !(objTipoPDF.checked ^ objTipoExcel.checked) )  throw new Error('Por favor, seleccione un tipo de informe');

		// Variables extra (para cada caso)
		var extraVars = this.getExtraVars();

		// Lanza el informe (por ahor, PDF)
		var url = "informe.php?fuente=informe&informe=" + objSelect.value;
		url += "&tipo=" + (objTipoPDF.checked ? "PDF" : "Excel");
		url += extraVars;
		url += "&rand=" + new Date().getMilliseconds();
		var ventana = new Ventana(url, 950, 700, true);
		if ( !ventana.mostrar() )  throw new Error("Tiene activado el bloqueador de ventanas emergentes. Por favor, desactívelo para este sitio web");
	}
	catch (e) {
		alert(e.message);
	}

}

/*
 * Método getExtraVars()
 *
 *  Obtiene las variables extra para el lanzamiento del informe: ruta del fichero de log 
 *   (en caso de seleccionar el informe de log), condición de búsqueda del informe y ordenación.
 *
 *  Parámetros: no tiene
 *  Devuelve: cadena para enviar por GET que complemente la llamada al generador de informes
 *  Lanza excepción si: - formato inesperado de la parametrización del informe
 */
Vista.prototype.getExtraVars = function () {

	var extraVars = "";
	var objSelect = document.getElementById("selInforme");

	if ( objSelect.value == "FICHERO" ) {
		var objFecha = document.getElementById("fechaLog");
		if ( objFecha.value == "" )  throw new Error("Es necesario cubrir la fecha");
		var fecha = new Fecha(objFecha.value);
		extraVars = "&ruta=" + fecha.getFechaSQL().replace(/-/g, "");
	}
	else {
		// Condición de búsqueda
		var condicionBusqueda = "";
		var descripCondicion = "";
		var arrayCondicionBusqueda = new Array();
		var arrayDescripCondicion = new Array();
		var camposFiltro = this.objInformes.registro[objSelect.selectedIndex - 1].CAMPOSFIL;
		if ( camposFiltro != "" ) {
			var arrayCamposFiltro = camposFiltro.split(" ");
			var arrayCampoFiltro = null;
			var objCampoFiltro, objCampoFiltro1, objCampoFiltro2;
			for ( var k in arrayCamposFiltro ) {
				arrayCampoFiltro = arrayCamposFiltro[k].split("|");
				if ( arrayCampoFiltro.length < 3 )  throw new Error("Error en la parametrización del informe");
				nombre = arrayCampoFiltro[0];
				descripcion = arrayCampoFiltro[1].replace(/_/g, " ");
				tipo = arrayCampoFiltro[2];
				switch ( tipo ) {
					case "A": {
						objCampoFiltro = document.getElementById("campoFiltro" + k);
						if ( objCampoFiltro.value == "" )  continue;
						arrayCondicionBusqueda.push(nombre + " LIKE '%" + objCampoFiltro.value.replace(/\'/g, "''") + "%'");
						arrayDescripCondicion.push(descripcion + " contiene '" + objCampoFiltro.value + "'");
						break;
					}
					case "D": {
						var bMenor = false, bMayor = false;
						objCampoFiltro1 = document.getElementById("campoFiltro" + k + "_desde");
						objCampoFiltro2 = document.getElementById("campoFiltro" + k + "_hasta");
						if ( (objCampoFiltro1.value == "") && (objCampoFiltro2.value == "") )  continue;
						aCondicion = new Array(), descripCondicion = "";
						if ( objCampoFiltro1.value != "" ) {
							valor = objCampoFiltro1.value.replace(/\./g, "");
							valor = valor.replace(/\,/g, ".");
							if ( isNaN(valor) )  throw new Error("Texto no válido: " + objCampoFiltro1.value);
							bMenor = true;
							aCondicion.push(nombre + " >= " + valor);
							//aDescripcion.push(descripcion + " es mayor o igual que " + objCampoFiltro1.value);
						}
						if ( objCampoFiltro2.value != "" ) {
							valor = objCampoFiltro2.value.replace(/\./g, "");
							valor = valor.replace(/\,/g, ".");
							if ( isNaN(valor) )  throw new Error("Texto no válido: " + objCampoFiltro2.value);
							bMayor = true;
							aCondicion.push(nombre + " <= " + valor);
							//aDescripcion.push(descripcion + " es mayor o igual que " + objCampoFiltro2.value);
						}
						descripCondicion = descripcion;
						if ( bMenor && bMayor ) {
							if ( objCampoFiltro1.value == objCampoFiltro2.value )  descripCondicion += " igual a " + objCampoFiltro1.value;
							else  descripCondicion += " entre " + objCampoFiltro1.value + " y " + objCampoFiltro2.value;
						}
						else if ( bMenor ) {
							descripCondicion += " mayor que " + objCampoFiltro1.value;
						}
						else {
							descripCondicion += " menor que " + objCampoFiltro2.value;
						}
						arrayCondicionBusqueda.push("(" + aCondicion.join(" AND ") + ")");
						arrayDescripCondicion.push(descripCondicion);
						break;
					}
					case "S":
					case "C": {
						objCampoFiltro1 = document.getElementById("campoFiltro" + k + "_S");
						objCampoFiltro2 = document.getElementById("campoFiltro" + k + "_N");
						if ( !(objCampoFiltro1.checked || objCampoFiltro2.checked) )  continue;
						aCondicion = new Array(), aDescripcion = new Array();
						if ( objCampoFiltro1.checked ) {
							aCondicion.push(nombre + " = 'S'");
							aDescripcion.push(descripcion + " igual a Sí");
						}
						if ( objCampoFiltro2.checked ) {
							aCondicion.push(nombre + (tipo == "S" ? " = 'N'" : " <> 'S'"));
							aDescripcion.push(descripcion + " igual a No");
						}
						arrayCondicionBusqueda.push("(" + aCondicion.join(" OR ") + ")");
						arrayDescripCondicion.push(aDescripcion.join(" o "));
						break;
					}
					case "G": {
						objCampoFiltro1 = document.getElementById("campoFiltro" + k + "_H");
						objCampoFiltro2 = document.getElementById("campoFiltro" + k + "_M");
						if ( !(objCampoFiltro1.checked || objCampoFiltro2.checked) )  continue;
						aCondicion = new Array(), aDescripcion = new Array();
						if ( objCampoFiltro1.checked ) {
							aCondicion.push(nombre + " = 'H'");
							aDescripcion.push(descripcion + " es Hombre");
						}
						if ( objCampoFiltro2.checked ) {
							aCondicion.push(nombre + " = 'M'");
							aDescripcion.push(descripcion + " es Mujer");
						}
						arrayCondicionBusqueda.push("(" + aCondicion.join(" OR ") + ")");
						arrayDescripCondicion.push(aDescripcion.join(" o "));
						break;
					}
					case "F": {
						var bMenor = false, bMayor = false;
						objCampoFiltro1 = document.getElementById("campoFiltro" + k + "_desde");
						objCampoFiltro2 = document.getElementById("campoFiltro" + k + "_hasta");
						if ( (objCampoFiltro1.value == "") && (objCampoFiltro2.value == "") )  continue;
						aCondicion = new Array(), descripCondicion = "";
						if ( objCampoFiltro1.value != "" ) {
							bMenor = true;
							aCondicion.push(nombre + " >= '" + new Fecha(objCampoFiltro1.value).getFechaSQL() + "'");
						}
						if ( objCampoFiltro2.value != "" ) {
							bMayor = true;
							aCondicion.push(nombre + " <= '" + new Fecha(objCampoFiltro2.value).getFechaSQL() + "'");
						}
						descripCondicion = descripcion;
						if ( bMenor && bMayor ) {
							if ( objCampoFiltro1.value == objCampoFiltro2.value )  descripCondicion += " igual a " + objCampoFiltro1.value;
							else  descripCondicion += " entre " + objCampoFiltro1.value + " y " + objCampoFiltro2.value;
						}
						else if ( bMenor ) {
							descripCondicion += " mayor que " + objCampoFiltro1.value;
						}
						else {
							descripCondicion += " menor que " + objCampoFiltro2.value;
						}
						arrayCondicionBusqueda.push("(" + aCondicion.join(" AND ") + ")");
						arrayDescripCondicion.push(descripCondicion);
						break;
					}
					default: {
						// Faltan los campos relacionados
						tipoChar = tipo.charAt(0);
						objCampoFiltro = document.getElementById("campoFiltro" + k);
						if ( objCampoFiltro.value == "" )  continue;
						valor = objCampoFiltro.value.replace(/\'/g, "''");
						if ( tipoChar == "A" )  valor = "'" + valor + "'";
						arrayCondicionBusqueda.push(nombre + " = " + valor);
						arrayDescripCondicion.push(descripcion + " es '" + objCampoFiltro.options[objCampoFiltro.selectedIndex].text + "'");
					}
				}
			}
		}
		// Condición
		condicionBusqueda = arrayCondicionBusqueda.join(" AND ");
		descripCondicion = arrayDescripCondicion.join(" y ");
		// Orden
		var objSelOrden = document.getElementById("selOrdenar");
		var objSelOrdenTipo = document.getElementById("selOrdenarTipo");
		var orderBy = objSelOrden.value + " " + objSelOrdenTipo.value;
		//if ( descripCondicion != "" )  descripCondicion += " - ";
		if ( descripCondicion != "" )  descripCondicion += "\n";
		descripCondicion += "Ordenado por " + objSelOrden.options[objSelOrden.selectedIndex].text + " " + objSelOrdenTipo.options[objSelOrdenTipo.selectedIndex].text;

		// Finalmente, las variables extra
		extraVars += "&condicion=" + condicionBusqueda.codificar();
		extraVars += "&descripcion=" + descripCondicion.codificar();
		extraVars += "&orden=" + orderBy.codificar();
		//alert(extraVars);
	}

	//alert(extraVars);
	return extraVars;

}

/*
 * Método getValor()
 *
 *  Obtiene el valor a filtrar convenientemente corregido, listo para la consulta a base de datos
 *
 *  Parámetros: valor: valor introducido en el filtro de búsquesa
 *              tipo: tipo de dato de este valor ('A', 'F' o 'D', por ahora)
 *  Devuelve: valor convenientemente corregido.
 *  Lanza excepción si: - texto no válido
 *                      - tipo de dato no conocido
 */
Vista.prototype.getValor = function (valor, tipo) {

	tipo = tipo.toUpperCase();

	switch ( tipo ) {
		case "D": { 
			valor = valor.replace(/\./g, "").replace(/,/g, ".");
			if ( (valor == "") || isNaN(valor) )  throw new Error("Texto a buscar no válido (se espera un número)");
			break; 
		}
		case "F": {
			var fecha = new Fecha(valor);
			valor = fecha.getFechaSQL();
			break;
		}
		case "A": {
			valor = valor.replace(/\'/g, "''");
			break;
		}
		default: { throw new Error("Tipo de dato no conocido: " + tipo); }
	}

	return valor;

}


}