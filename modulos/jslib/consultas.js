/*
 *  Funcionalidades espec�ficas de la pantalla de consultas
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 20/12/2006
 */

// Control de c�digo: ha de estar incluido el fichero 'Vista.js'
if ( typeof Vista != "undefined" ) {


/*
 * M�todo inicio()
 *
 *  Realiza las operaciones pertinentes tras la carga de la p�gina. Sobrepone al que hay en 'Vista.js'
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Vista.prototype.inicio = function () {

	// M�todos de la clase base
	this.setBotonArriba();

	// Sustituimos el filtro por un campo para poder cubrir la consulta
	var textoFiltro = 'Escriba la consulta que desea realizar:<p />';
	textoFiltro += '<textarea id="CONSULTA" rows="3" cols="105"></textarea><p />';
	textoFiltro += '<table width="95%"><tr><td align="left">';
	textoFiltro += '<input type="button" id="btnLimpiar" value="Limpiar" class="boton" onmouseover="javascript: if ( !this.disabled ) { this.className=\'botonOver\'; };" onmouseout="javascript: this.className=\'boton\';" onclick="javascript: vista.limpiarConsulta();" style="width: 150px;"/>';
	textoFiltro += '</td><td align="right">';
	textoFiltro += '<input type="button" id="btnConsultar" value="Consultar" class="boton" onmouseover="javascript: if ( !this.disabled ) { this.className=\'botonOver\'; };" onmouseout="javascript: this.className=\'boton\';" onclick="javascript: vista.preConsulta();" style="width: 150px;"/>';
	textoFiltro += '</td></tr></table>';

	document.getElementById("inFiltro").innerHTML = textoFiltro;
	document.getElementById("extFiltro").style.display = "";

}

/*
 * M�todo limpiarConsulta()
 *
 *  Realiza las acciones vinculadas con el bot�n de limpiar
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Vista.prototype.limpiarConsulta = function () {

	document.getElementById("CONSULTA").value = "";
	document.getElementById("extResultado").style.display = "none";
	document.getElementById("extNavegador").style.display = "none";

}

/*
 * M�todo preConsulta()
 *
 *  Realiza una llamada previa a la consulta para obtener los nombres de los campos
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Vista.prototype.preConsulta = function () {

	// Primero buscamos los campos de resultado

	try {
		var consulta = document.getElementById("CONSULTA").value;
		var objAjax = new AJAXRequest();
		objAjax.setUrl("servidor.php");
		objAjax.addParametro("tipoServidor", "Consulta");
		objAjax.addParametro("numAcciones", "1");
		objAjax.addParametro("sql", consulta);
		//alert(consulta);
		//objAjax.addParametro("plantilla", this.plantilla);
		objAjax.addParametro("numFilas", 1);
		objAjax.addParametro("filaInicial", 0);
		// Informaci�n para log
		objAjax.addParametro("usuario", this.pantalla.usuario);
		objAjax.addParametro("libtabla", '');
		var objThis = this;
		objAjax.setFuncionDeVuelta(function () { objThis.vueltaPreConsulta(objAjax); });
		objAjax.enviar();
		// Mensaje de b�squeda
		this.mostrarAccion("REALIZANDO B�SQUEDA...");
	}
	catch (e) {
		alert(e);
	}

}

/*
 * M�todo vueltaPreConsulta()
 *
 *  Recupera los campos de la consulta y realiza la llamada en s�
 *
 *  Par�metros: objAjax: objeto AJAXRequest con el resultado de la consulta
 *  Devuelve: nada
 */
Vista.prototype.vueltaPreConsulta = function (objAjax) {

	try {
		if ( !objAjax.finCarga() )  return;
		if ( objAjax.hayErrorHTTP() )  throw new Error(objAjax.getErrorHTTP());

		//alert(objAjax.objHttpRequest.responseText);
		var objResultado = objAjax.getRespuestaJSON().resultado_consulta;
		//alert(objResultado.campos);
		
		// Control de error:
		if ( objResultado.error != "" )  throw new Error(objResultado.error);

		// Ahora tenemos los campos para hacer la cabecera y la plantilla
		var arrayCampos = objResultado.campos.split(",");
		this.setCabeceraYPlantilla(arrayCampos);

		// Consulta en s�
		this.consulta(arrayCampos);

	}
	catch (e) {
		alert(e.message);
		this.ocultarAccion(true);
	}


}

/*
 * M�todo setCabeceraYPlantilla()
 *
 *  Genera la cabecera y la plantilla de resultado de esta consulta
 *
 *  Par�metros: arrayCampos: lista con los nombres de los campos de resultado
 *  Devuelve: nada
 */
Vista.prototype.setCabeceraYPlantilla = function (arrayCampos) {

	var cabecera = '<tr class="clCabeceraResultado">';
	var plantilla = '<tr class="clFila" onmouseover="javascript: this.className=\'clFilaOver\';" onmouseout="javascript: this.className=\'clFila\';">';
	for ( var k in arrayCampos ) {
		cabecera += '<td align="center">' + arrayCampos[k].toUpperCase() + '</td>';
		plantilla += '<td>{$' + arrayCampos[k] + '}</td>';
	}
	cabecera += '</tr>';
	plantilla += '</tr>';

	this.cabecera = cabecera;
	this.plantilla = plantilla;
	//alert(this.plantilla);

}

/*
 * M�todo consulta()
 *
 *  Realiza la consulta que se ha cubierto
 *
 *  Par�metros: arrayCampos: lista con los nombres de los campos de resultado
 *  Devuelve: nada
 */
Vista.prototype.consulta = function (arrayCampos) {

	this.pantalla.selectResultado = document.getElementById("CONSULTA").value;
	this.pantalla.campoOrden = arrayCampos[0];

	this.buscar(true);

}


}