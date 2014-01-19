/*
 * Clase Edicion.
 *
 *  Representa las principales operaciones relacionadas con la edici�n de un registro de una pantalla.
 *
 *  IMPORTANTE: debe tener incluido antes los siguientes ficheros: 'String.js', 'AJAXRequest.js', 
 *   'Fecha.js' y 'Ventana.js'.
 * 
 *  Autor: Pentared
 *  �ltima actualizaci�n: 30/01/2007
 */

/*
 * Constructor de la clase
 *
 *  Realiza las operaciones iniciales para la edici�n de una vista: asignaci�n del objeto pantalla.
 *
 *  Par�metros: pantalla: c�digo de la pantalla: 
 */
function Edicion (pantalla) {
	
	// Asignaci�n de objeto de pantalla
	this.pantalla = pantalla;

}

/*
 * M�todo preInicio
 *
 *  Reservado para que las pantallas hagan sus operaciones espec�ficas.
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.preInicio = function () {

}

/*
 * M�todos inicio(), inicio2()
 *
 *  Realizan las operaciones iniciales tras la carga de la p�gina: configurar permisos de los botones,
 *   rellenar combos de campos relacionados, campos fijos y, si procede, recuperar los datos del registro.
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.inicio = function () {

	// Inicio de cada usuario
	this.preInicio();

	// Permisos sobre los campos y acciones
	this.setPermisos();

	// Campos relacionados y campos fijos

	// Chapuza por culpa del IE: la carga de los combos no se produce correctamente si se hace,
	//  m�s de uno consecutivamente, con lo que hay que separarlos con un timeout.
	// Esta llamada se ir� produciendo desde k = 0 hasta k = n�mero de campos de edici�n de la
	//  pantalla, rellenando aquellos que sean campos relacionados. Cuando llegue al �ltimo campo, 
	//  seguir� el flujo de la aplicaci�n en el m�todo inicio2()
	this.rellenarRelacionado(0);
	// Cuando averigue c�mo resolverlo, llamar a estos m�todos consecutivamente
	//this.rellenarRelacionados();
	//this.inicio2();

}
Edicion.prototype.inicio2 = function () {

	this.rellenarFijos();

	if ( this.pantalla.clave == "" )  this.finRecuperar();
	else  this.recuperar();

}

/*
 * M�todo setPermisos()
 *
 *  Pone los permisos de los botones y los campos adecuadamente
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.setPermisos = function () {

	// Botonera
	document.getElementById("btnNuevo").style.display = (this.pantalla.permisoModificar == "S" ? "" : "none");
	document.getElementById("btnLimpiar").style.display = (this.pantalla.permisoModificar == "S" ? "" : "none");
	document.getElementById("btnModificar").style.display = ((this.pantalla.permisoModificar == "S") && (this.pantalla.clave != "") ? "" : "none");

	// Poner campos de salida (si procede)
	if ( (this.pantalla.permisoModificar != "S") && (this.pantalla.edicionEspecifica != "") ) {
		var objCampo, objCampoEnPantalla;
		for ( var k in this.pantalla.camposGestion ) {
			objCampo = this.pantalla.camposGestion[k];
			objCampoEnPantalla = document.getElementById(objCampo.nombre);
			switch ( objCampo.tipo ) {
				case "S": {
					document.getElementById(objCampo.nombre + "_S").disabled = true;
					document.getElementById(objCampo.nombre + "_N").disabled = true;
					break;
				}
				case "G": {
					document.getElementById(objCampo.nombre + "_H").disabled = true;
					document.getElementById(objCampo.nombre + "_M").disabled = true;
					break;
				}
				case "F": {
					var objCalendario = document.getElementById("cal_" + objCampo.nombre);
					if ( objCalendario )  objCalendario.style.display = "none";
				}
				default: { objCampoEnPantalla.readOnly = objCampoEnPantalla.disabled = true; }
			}
			// Campo relacionado en diferido
			if ( objCampo.descRelacionado != "" ) {
				// Bot�n para abrir lista
				objCampoEnPantalla = document.getElementById("btn_" + objCampo.nombre);
				if ( objCampoEnPantalla )  objCampoEnPantalla.style.display = "none";
				// Bot�n para borrar valores
				objCampoEnPantalla = document.getElementById("btnDel_" + objCampo.nombre);
				if ( objCampoEnPantalla )  objCampoEnPantalla.style.display = "none";
			}
		}
	}

}

/*
 * M�todo rellenarRelacionados()
 *
 *  Rellena los combos de campos relacionados de la pantalla.
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.rellenarRelacionados = function () {

	var objCampo, objCampoPantalla, objJSON, objOption;
	var codigo, descripcion;

	for ( var k in this.pantalla.camposGestion ) {
		objCampo = this.pantalla.camposGestion[k];
		if ( (objCampo.relacionado == "") || (objCampo.descRelacionado != "") )  continue;
		objCampoPantalla = document.getElementById(objCampo.nombre);
		objJSON = eval("obj" + objCampo.nombre + ".resultado_consulta");
		for ( var n in objJSON.registro ) {
			codigo = eval("objJSON.registro[" + n + "]." + objCampo.nombre);
			descripcion = eval("objJSON.registro[" + n + "].DESCRIPCION");
			objOption = document.createElement("option");
			objOption.value = codigo;
			objOption.innerHTML = descripcion.htmlEncode();
			objCampoPantalla.appendChild(objOption);
		}
	}

}

/*
 * M�todo rellenarRelacionado()
 *
 *  Rellena el combo de campo relacionado k-�simo, si es que este campo lo es,
 *   y temporiza para rellenar el siguiente, o bien contin�a con la carga de la
 *   p�gina, en caso de haber llegado al �ltimo campo
 *
 *  Par�metros: k: �ndice de campo de la pantalla
 *  Devuelve: nada
 */
Edicion.prototype.rellenarRelacionado = function (k) {

	// Se acabaron los campos de la pantalla: seguimos con la carga de la p�gina
	if ( k >= this.pantalla.camposGestion.length ) {
		this.inicio2();
		return;
	}

	var objCampo, objCampoPantalla, objJSON, objOption;
	var codigo, descripcion;
	objCampo = this.pantalla.camposGestion[k++];
	if ( (objCampo.relacionado != "") && (objCampo.descRelacionado == "") ) {
		// El campo es relacionado directo
		objCampoPantalla = document.getElementById(objCampo.nombre);
		objJSON = eval("obj" + objCampo.nombre + ".resultado_consulta");
		for ( var n in objJSON.registro ) {
			codigo = eval("objJSON.registro[" + n + "]." + objCampo.nombre);
			descripcion = eval("objJSON.registro[" + n + "].DESCRIPCION");
			objOption = document.createElement("option");
			objOption.value = codigo;
			objOption.innerHTML = descripcion.htmlEncode();
			objCampoPantalla.appendChild(objOption);
		}
		// Nos dirigimos al siguiente
		window.setTimeout("edicion.rellenarRelacionado(" + k + ")", 1);
	}
	else {
		// El campo no es relacionado directo
		this.rellenarRelacionado(k);
	}

}

/*
 * M�todo rellenarFijos()
 *
 *  Rellena los campos fijos de la pantalla con sus valores fijos y los deshabilita
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.rellenarFijos = function () {

	if ( this.pantalla.camposFijos == "" )  return;

	var arrayCamposFijos = this.pantalla.camposFijos.split("|");
	var arrayValoresFijos = this.pantalla.valoresFijos.split("|");

	var objCampoEnPantalla = null;
	for ( var k in arrayCamposFijos ) {
		objCampoEnPantalla = document.getElementById(arrayCamposFijos[k]);
		if ( !objCampoEnPantalla )  continue;
		objCampoEnPantalla.value = arrayValoresFijos[k];		
		objCampoEnPantalla.readOnly = objCampoEnPantalla.disabled = true;
		// Caso relacionado en diferido
		objCampoEnPantalla = document.getElementById("btn_" + arrayCamposFijos[k]);
		if ( objCampoEnPantalla )  objCampoEnPantalla.style.display = "none";
		objCampoEnPantalla = document.getElementById("btnDel_" + arrayCamposFijos[k]);
		if ( objCampoEnPantalla )  objCampoEnPantalla.style.display = "none";
	}

}

/*
 * M�todo recuperar()
 *
 *  Realiza la llamada para recuperar el registro que se ha cargado
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.recuperar = function () {

	try {
		// Condici�n del registro a recuperar
		var consulta = this.pantalla.selectResultado;
		if ( consulta.toUpperCase().indexOf(" WHERE ") == -1 )  consulta += " WHERE ";
		else  consulta += " AND ";
		consulta += this.pantalla.clave;
		// Objeto para llamada as�ncrona
		var objAjax = new AJAXRequest();
		objAjax.setUrl("../modulos/servidor.php");
		objAjax.addParametro("tipoServidor", "Consulta");
		objAjax.addParametro("numAcciones", "1");
		objAjax.addParametro("sql", consulta);
		//alert(consulta);
		// Informaci�n para log
		objAjax.addParametro("usuario", this.pantalla.usuario);
		objAjax.addParametro("libtabla", this.pantalla.libreriaTabla);
		var objThis = this;
		objAjax.setFuncionDeVuelta(function () { objThis.vueltaRecuperar(objAjax); });
		objAjax.enviar();
		// Mensaje de b�squeda
		this.mostrarAccion("RECUPERANDO REGISTRO...");
	}
	catch (e) {
		alert(e.message);
	}

}

/*
 * M�todo vueltaRecuperar()
 *
 *  Gestiona la recuperaci�n de los datos del registro
 *
 *  Par�metros: objAjax: objeto AJAXRequest con la consulta del registro
 *  Devuelve: nada
 */
Edicion.prototype.vueltaRecuperar = function (objAjax) {

	try {
		if ( !objAjax.finCarga() )  return;
		if ( objAjax.hayErrorHTTP() )  throw new Error(objAjax.getErrorHTTP());

		//alert(objAjax.objHttpRequest.responseText);
		var objResultado = objAjax.getRespuestaJSON().resultado_consulta;
		//alert(objetoRespuesta.contenido);
		
		// Control de error:
		if ( objResultado.error != "" )  throw new Error(objResultado.error);

		// Control de resultado existente
		if ( objResultado.numResultados == 0 )  throw new Error("El registro al que pretende acceder no existe");

		// Pintamos el resultado
		var objCampo, objCampoEnPantalla;
		var valor, campoDesc;
		for ( var k in this.pantalla.camposGestion ) {
			objCampo = this.pantalla.camposGestion[k];
			//alert(objCampo.nombre);
			valor = eval("objResultado.registro[0]." + objCampo.nombre);
			if ( typeof valor == "undefined" )  throw new Error("No existe el campo '" + objCampo.nombre + "' en el resultado");
			valor = valor.descodificar();
			objCampoEnPantalla = document.getElementById(objCampo.nombre);
			//if ( !objCampoEnPantalla )  throw new Error("No existe el campo '" + objCampo.nombre + "' en el pantalla");
			switch ( objCampo.tipo ) {
				case "C": {
					document.getElementById(objCampo.nombre).checked = (valor == "S");
					break;
				}
				case "S": {
					document.getElementById(objCampo.nombre + "_S").checked = (valor == "S");
					document.getElementById(objCampo.nombre + "_N").checked = (valor == "N");
					break;
				}
				case "G": {
					document.getElementById(objCampo.nombre + "_H").checked = (valor == "H");
					document.getElementById(objCampo.nombre + "_M").checked = (valor == "M");
					break;
				}
				default: objCampoEnPantalla.value = valor;
			}
			// Descripci�n de campo relacionado en diferido
			if ( objCampo.descRelacionado != "" ) {
				campoDesc = objCampo.descRelacionado;
				valor = eval("objResultado.registro[0]." + campoDesc);
				if ( typeof valor == "undefined" )  throw new Error("No existe el campo '" + campoDesc + "' en el resultado");
				valor = valor.descodificar();
				objCampoEnPantalla = document.getElementById(campoDesc);
				if ( !objCampoEnPantalla ) throw new Error("No existe el campo '" + campoDesc + "' en la pantalla");
				objCampoEnPantalla.value = valor;
			}
		}
	}
	catch (e) {
		alert(e.message);
		document.getElementById("btnModificar").disabled = true;
	}

	// Llamada al m�todo de fin de recuperaci�n
	this.finRecuperar();

}

/*
 * M�todo finRecuperar()
 *
 *  M�todo que se llama tras acabar con las tareas de fin de recuperaci�n. Si se quiere hacer algo m�s,
 *   sobreponer este m�todo.
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.finRecuperar = function () {

	this.accionFinRecuperar();

}

/*
 * M�todo accionFinRecuperar()
 *
 *  M�todo que se llama tras acabar con las tareas de fin de recuperaci�n. Si se ha sobrepuesto 
 *   finRecuperar(), es conveniente llamar a este al acabar las tareas espec�ficas.
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.accionFinRecuperar = function () {

	this.ocultarAccion();

}

/*
 * M�todo limpiar()
 *
 *  M�todo que realiza una borrado de los elementos de la edici�n, exceptuando aquellos
 *   campos que son fijos.
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.limpiar = function () {

	// Lista de campos fijos para comparar
	var listaCamposFijos = " " + this.pantalla.camposFijos.replace(/\|/g, " ") + " ";

	// Recorremos los campos y los borramos
	var objCampo = null, objCampoEnPantalla = null;
	for ( var k in this.pantalla.camposGestion ) {
		objCampo = this.pantalla.camposGestion[k];
		// Si es un campo fijo, no lo borramos
		if ( listaCamposFijos.indexOf(" " + objCampo.nombre + " ") > -1 )  continue;
		objCampoEnPantalla = document.getElementById(objCampo.nombre);
		switch ( objCampo.tipo ) {
			case "C": { 
				objCampoEnPantalla.checked = false; break; 
			}
			case "S": { 
				document.getElementById(objCampo.nombre + "_S").checked = false;
				document.getElementById(objCampo.nombre + "_N").checked = false;
				break; 
			}
			case "G": { 
				document.getElementById(objCampo.nombre + "_H").checked = false;
				document.getElementById(objCampo.nombre + "_M").checked = false;
				break; 
			}
			default: { objCampoEnPantalla.value = ""; }
		}
		// Adicional si es un campo relacionado en diferido
		if ( objCampo.descRelacionado != "" ) {
			document.getElementById(objCampo.descRelacionado).value = "";
		}
	}

	// Llamada a la funci�n para heredar
	this.finLimpiar();

}

/*
 * M�todo finLimpiar()
 *
 *  M�todo que se ha de sobreponer por el c�digo espec�fico para hacer acciones adicionales
 *   tras pulsar en el bot�n de borrado.
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.finLimpiar = function () {


}

/*
 * M�todo nuevo()
 *
 *  M�todo al que hay que llamar para desencadenar el proceso de alta de un nuevo registro. Est�
 *   vinculado a la acci�n onclick del bot�n de Nuevo de la edici�n
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.nuevo = function () {
	
	this.accionNuevo();

}

/*
 * M�todo accionNuevo()
 *
 *  Realiza la llamada para insertar un nuevo registro en la pantalla
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.accionNuevo = function () {

	// Control de permisos (no deber�a entrar nunca aqu�)
	if ( this.pantalla.permisoModificar != "S" )  return;

	// Creaci�n de los campos y valores de inserci�n
	var arrayCamposInsert = new Array();
	var arrayValoresInsert = new Array();
	var campoInsert, valorInsert;
	var tipo, obligatorio;
	var objCampo;

	try {
		// Creaci�n de los campos y valores de inserci�n
		for ( var k in this.pantalla.camposGestion ) {
			objCampo = this.pantalla.camposGestion[k];
			obligatorio = objCampo.obligatorio;
			if ( obligatorio == "A" )  continue;
			campoInsert = objCampo.nombre;
			//valorInsert = document.getElementById(campoInsert).value.trim();
			valorInsert = this.getValor(objCampo);
			if ( (valorInsert == "") && (obligatorio != "N") ) {
				throw new Error("Debe rellenar el campo '" + objCampo.descripcion + "'");
			}
			tipo = objCampo.tipo;
			if ( valorInsert == "" ) {
				valorInsert = "NULL";
			}
			else if ( (tipo == "A") || (tipo == "C") ||(tipo == "S") || (tipo == "G") || (tipo == "F") ) {
				valorInsert = "'" + valorInsert.replace(/\'/g, "''").replace(/\r,\n/g, "") + "'";
			}
			arrayCamposInsert.push(campoInsert);
			arrayValoresInsert.push(valorInsert);
		}

		// Llamada al servidor
		var objAjax = new AJAXRequest();
		objAjax.setUrl("servidor.php");
		objAjax.addParametro("tipoServidor", "Ejecuta");
		objAjax.addParametro("numAcciones", "1");
		objAjax.addParametro("tipoOperacion1", "INSERT");
		objAjax.addParametro("libtabla1", this.pantalla.libreriaTabla);
		objAjax.addParametro("camposInsert1", arrayCamposInsert.join(", "));
		objAjax.addParametro("valoresInsert1", arrayValoresInsert.join(", "));
		// Informaci�n para log
		objAjax.addParametro("usuario", this.pantalla.usuario);
		var objThis = this;
		objAjax.setFuncionDeVuelta(function () { objThis.vueltaEjecutar(objAjax); });
		objAjax.enviar();
		// Mensaje
		this.mostrarAccion("CREANDO ELEMENTO...");
	}
	catch (e) {
		alert(e.message);
	}

}

/*
 * M�todo modificar()
 *
 *  M�todo al que hay que llamar para desencadenar el proceso de modificaci�n de un registro. Est�
 *   vinculado a la acci�n onclick del bot�n de Modificar de la edici�n
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.modificar = function () {

	this.accionModificar();

}

/*
 * M�todo accionModificar()
 *
 *  Realiza la llamada para actualizar un registro de la pantalla
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.accionModificar = function () {

	// Control de permisos (no deber�a entrar nunca aqu�)
	if ( this.pantalla.permisoModificar != "S" )  return;

	// Creaci�n de los campos de actualizaci�n
	var arrayCamposUpdate = new Array();
	var tipo, obligatorio;
	var campoUpdate, valorUpdate;
	var objCampo;

	try {
		// Validaci�n de los campos
		for ( var k in this.pantalla.camposGestion ) {
			objCampo = this.pantalla.camposGestion[k];
			obligatorio = objCampo.obligatorio;
			if ( obligatorio == "A" )  continue;
			campoUpdate = objCampo.nombre;
			//valorUpdate = document.getElementById(campoUpdate).value;
			valorUpdate = this.getValor(objCampo);
			tipo = objCampo.tipo;
			if ( valorUpdate == "" ) {
				valorUpdate = "NULL";
			}
			else if ( (tipo == "A") || (tipo == "C") ||(tipo == "S") || (tipo == "G") || (tipo == "F") ) {
				valorUpdate = "'" + valorUpdate.replace(/\'/g, "''").replace(/\r,\n/g, "") + "'";
			}
			arrayCamposUpdate.push(campoUpdate + " = " + valorUpdate);
		}
		// Llamada al servidor
		var objAjax = new AJAXRequest();
		objAjax.setUrl("servidor.php");
		objAjax.addParametro("tipoServidor", "Ejecuta");
		objAjax.addParametro("numAcciones", "1");
		objAjax.addParametro("tipoOperacion1", "UPDATE");
		objAjax.addParametro("libtabla1", this.pantalla.libreriaTabla);
		objAjax.addParametro("camposUpdate1", arrayCamposUpdate.join(", "));
		objAjax.addParametro("condicionUpdate1", this.pantalla.clave);
		// Informaci�n para log
		objAjax.addParametro("usuario", this.pantalla.usuario);
		var objThis = this;
		objAjax.setFuncionDeVuelta(function () { objThis.vueltaEjecutar(objAjax); });
		objAjax.enviar();
		// Mensaje
		this.mostrarAccion("MODIFICANDO ELEMENTO...");
	}
	catch (e) {
		alert(e.message);
	}

}

/*
 * M�todo vueltaEjecutar()
 *
 *  Gestiona el procesamiento del resultado de la operaci�n realizada (nuevo o modificaci�n)
 *
 *  Par�metros: objAjax: objeto AJAXRequest con el resultado de la operaci�n
 *  Devuelve: nada
 */
Edicion.prototype.vueltaEjecutar = function (objAjax) {

	if ( !objAjax.finCarga() )  return;
	if ( objAjax.hayErrorHTTP() ) {
		alert(objAjax.getErrorHTTP());
		return;
	}
	//alert(objAjax.objHttpRequest.responseText);
	var objResultado = objAjax.getRespuestaJSON().resultado_consulta;
	
	// Control de error
	var error = objResultado.operacion[0].error;
	if ( error != "" ) {
		alert(error);
		this.ocultarAccion();
		return;
	}

	// Se ha realizado la ejecuci�n con �xito: actualizamos la ventana llamante y salimos
	this.cerrarVentana();

}

/*
 * M�todo cerrarVentana()
 *
 *  Cierra la ventana actualizando la ventana llamante (opener). Se puede sobreponer por los
 *   ficheros espec�ficos
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.cerrarVentana = function () {

	if ( window.opener && window.opener.vista )  window.opener.setTimeout("vista.buscar(false)", 1000);
	window.setTimeout("window.close()", 1000);

}


/*
 * M�todo getValor()
 *
 *  Obtiene el valor de un campo dado, considerando su tipo de datos
 *
 *  Par�metros: objCampo: objeto con la parametrizaci�n del campo
 *  Devuelve: valor del campo indicado
 */
Edicion.prototype.getValor = function (objCampo) {

	var valor = "";

	switch ( objCampo.tipo ) {
		case "F": {
			// Fecha
			var fecha = new Fecha(document.getElementById(objCampo.nombre).value);
			valor = fecha.getFechaSQL();
			break;
		}
		case "C": {
			// Check
			if ( document.getElementById(objCampo.nombre).checked )  valor = "S";
			else  valor = "N";
			break;
		}
		case "S": {
			// 'S�' o 'No'
			if ( document.getElementById(objCampo.nombre + "_S").checked )  valor = "S";
			if ( document.getElementById(objCampo.nombre + "_N").checked )  valor = "N";
			break;
		}
		case "G": {
			// 'Hombre' o 'Mujer'
			if ( document.getElementById(objCampo.nombre + "_H").checked )  valor = "H";
			if ( document.getElementById(objCampo.nombre + "_M").checked )  valor = "M";
			break;
		}
		case "D": {
			// Num�rico
			valor = document.getElementById(objCampo.nombre).value;
			valor = valor.replace(/\./g, "").replace(/,/g, ".");
			if ( isNaN(valor) ) throw new Error("Formato de n�mero no v�lido en el campo '" + objCampo.descripcion + "'");
			break;
		}
		default: { valor = document.getElementById(objCampo.nombre).value; }
	}

	return valor;
	
}

/*
 * M�todo controlMaxLength()
 *
 *  Realiza la funci�n 'maxlength' para un textarea, vincul�ndolo con el evento 'onkeypress' de dicho objeto
 *
 *  Fuente: http://groups.google.de/group/de.comp.lang.javascript/tree/browse_frm/month/2003-05/bb94461c875f0c73?rnum=61&_done=%2Fgroup%2Fde.comp.lang.javascript%2Fbrowse_frm%2Fmonth%2F2003-05%3F
 *  Par�metros: control: objeto textarea a controlar
 *              evt: objeto con el evento generado
 *              maxLength: longitud m�xima del campo
 *              allowedKeys: conjunto de caracteres permitidos
 *  Devuelve: 'true' si ha permitido el car�cter, 'false' en caso contrario
 */
Edicion.prototype.controlMaxLength = function (control, evt, maxLength, allowedKeys) {

	if ( control.value.length < maxLength ) {
		return true;
	}
	else {
		if ( typeof allowedKeys == 'undefined' ) {
			allowedKeys = { backspace: 8, deleteKey: 46, cursorLeft: 37, cursorRight: 39, cursorDown: 40, cursorUp: 38 };
		}
		var keyCode = evt.keyCode ? evt.keyCode : evt.charCode ? evt.charCode : evt.which;
		if ( keyCode ) {
			for ( var keyName in allowedKeys ) {
				if ( allowedKeys[keyName] == keyCode ) {
					return true;
				}
			}
			return false;
		}
		else {
			return true;
		}
   }	

}

/*
 * M�todo abrirLista()
 *
 *  Abre una ventana de vista como lista
 *
 *  Par�metros: pantalla: pantalla que hay que abrir como vista
 *  Devuelve: camposLista: campos para el funcionamiento como lista
 */
Edicion.prototype.abrirLista = function (pantalla, camposLista) {

	//alert(pantalla + ": " + campos);
	// Direcci�n de la lista
	var url = "vista.php?lista=S&pantalla=" + pantalla;
	url += "&usuario=" + this.pantalla.usuario;
	url += "&camposLista=" + camposLista.codificar();

	// Ventana emergente
	var ventana = new Ventana(url, 730, 500, true);
	ventana.scrollbars = true;
	try {
		if ( !ventana.mostrar() )  throw new Error("Tiene activado el bloqueador de ventanas emergentes. Por favor, desact�velo para este sitio web");
	}
	catch (e) {
		alert(e.message);
	}

}

/*
 * M�todo volverLista()
 *
 *  Realiza las acciones relacionadas con la vuelta de la lista
 *
 *  Par�metros: arrayCampos: lista de campos en los que se volcar�n los datos devueltos por la lista
 *              arrayValores: valores devueltos por la lista
 *  Devuelve: nada
 */
Edicion.prototype.volverLista = function (arrayCampos, arrayValores) {

	for ( var k in arrayCampos ) {
		document.getElementById(arrayCampos[k]).value = arrayValores[k];
	}

	// Acciones extra despu�s de volver de la lista
	this.finVolverLista(arrayCampos);

}

/*
 * M�todo finVolverLista()
 *
 *  Realiza las acciones posteriores a la vuelta de la lista. �til para sobreponer con los
 *   ficheros espec�ficos
 *
 *  Par�metros: arrayCampos: lista de campos en los que se han volcado los datos devueltos por la lista
 *  Devuelve: nada
 */
Edicion.prototype.finVolverLista = function (arrayCampos) {

}

/*
 * M�todo borrarRelacionado()
 *
 *  Borra el valor de un campo relacionado en diferido y su descripci�n
 *
 *  Par�metros: campo: nombre del campo relacionado
 *              descCampo: nombre del campo descripci�n
 */
Edicion.prototype.borrarRelacionado = function (campo, descCampo) {

	// Borrado de los campos
	document.getElementById(campo).value = "";
	document.getElementById(descCampo).value = "";

	// Llamada a la funci�n a heredar
	this.finBorrarRelacionado(campo, descCampo);

}

/*
 * M�todo finBorrarRelacionado()
 *
 *  Acciones a realizar tras borrar un campo relacionado. Debe
 *   ser sobrepuesta por un fichero de JavaScript espec�fico
 *
 *  Par�metros: campo: nombre del campo relacionado
 *              descCampo: nombre del campo descripci�n
 *  Devuelve: nada
 */
Edicion.prototype.finBorrarRelacionado = function (campo, descCampo) {

}

/*
 * M�todo mostrarAccion()
 *
 *  Muestra el texto indicado en el recuadro de acci�n
 *
 *  Par�metros: msgAccion: mensaje a mostrar
 *  Devuelve: nada
 */
Edicion.prototype.mostrarAccion = function (msgAccion) {

	document.getElementById("botones").style.display = "none";

	document.getElementById("textoAccion").innerHTML = msgAccion.htmlEncode();
	document.getElementById("extMsgAccion").style.display = "";

}

/*
 * M�todo ocultarAccion()
 *
 *  Oculta el texto del recuadro de acci�n
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.ocultarAccion = function () {

	document.getElementById("extMsgAccion").style.display = "none";
	document.getElementById("botones").style.display = "";
	
}