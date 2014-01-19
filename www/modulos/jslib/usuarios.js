/*
 *  Funcionalidades espec�ficas de la pantalla de usuarios del sistema
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 20/12/2006
 */

// Control de c�digo: ha de estar incluido el fichero 'Edicion.js'
if ( typeof Edicion != "undefined" ) {


/*
 * M�todo nuevo()
 *
 *  Realiza las operaciones pertinentes tras pulsar el bot�n 'Nuevo'. Sobrepone al que hay en 'Edicion.js'
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.nuevo = function () {

	this.accionUsuarios("nuevo");

}

/*
 * M�todo nuevo()
 *
 *  Realiza las operaciones pertinentes tras pulsar el bot�n 'Modificar'. Sobrepone al que hay en 'Edicion.js'
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.modificar = function () {

	this.accionUsuarios("modificar");

}

/*
 * M�todo accionUsuarios()
 *
 *  Realiza las operaciones pertinentes tras pulsar en 'Nuevo' o 'Modificar': obtener los
 *   valores encriptados del usuario y la contrase�a
 *
 *  Par�metros: accion: acci�n seleccionada ('nuevo' o 'modificar')
 *  Devuelve: nada
 */
Edicion.prototype.accionUsuarios = function (accion) {

	// Comprobaci�n previa de campos
	var usuario = document.getElementById("USUARIO").value;
	var nombre = document.getElementById("DESCRIPCION").value;
	var password = document.getElementById("PASSWORD").value;
	var password1 = document.getElementById("PASSWORD_clara1").value;
	var password2 = document.getElementById("PASSWORD_clara2").value;

	try {
		// Control de par�metros
		if ( usuario == "" )  throw new Error("Debe cubrir el c�digo de usuario");
		if ( nombre == "" )  throw new Error("Debe cubrir el nombre de usuario");
		var bCambioPassword = this.gestionaPasswords(usuario, password, password1, password2, accion);
		var expresion1 = "crypt('" + usuario + "', '" + clave + "')";
		var expresion2 = "crypt('" + password1 + "', '" + clave + "')";

		var objAjax = new AJAXRequest();
		objAjax.setUrl("servidor.php");
		objAjax.addParametro("tipoServidor", "Evalua");
		objAjax.addParametro("numAcciones", "2");
		objAjax.addParametro("expresion1", expresion1);
		objAjax.addParametro("expresion2", expresion2);
		
		var objThis = this;
		objAjax.setFuncionDeVuelta(function () { objThis.vueltaAccionUsuarios(objAjax, accion, bCambioPassword); });
		objAjax.enviar();
	}
	catch (e) {
		alert(e.message);
	}

}

/*
 * M�todo vueltaAccionUsuarios()
 *
 *  Comprueba que se han obtenido con �xito los valores requeridos por accionUsuarios()
 *
 *  Par�metros: objAjax: objeto AJAXRequest con el resultado de la consulta
 *              accion: acci�n original
 *              bCambioPassword: 'true' si se ha cambiado la contrase�a; 'false' en caso contrario
 *  Devuelve: nada
 */
Edicion.prototype.vueltaAccionUsuarios = function (objAjax, accion, bCambioPassword) {

	try {
		if ( !objAjax.finCarga() )  return;
		if ( objAjax.hayErrorHTTP() )  throw new Error(objAjax.getErrorHTTP());

		//alert(objAjax.objHttpRequest.responseText);
		var objResultado = objAjax.getRespuestaJSON().resultado_consulta;
		//alert(objetoRespuesta.contenido);
		
		// Control de error:
		if ( objResultado.error != "" )  throw new Error(objResultado.error);

		// Valores
		document.getElementById("CUSUARIO").value = objResultado.accion[0].contenido;
		if ( bCambioPassword )  document.getElementById("PASSWORD").value = objResultado.accion[1].contenido;
	
		// Acci�n original
		switch ( accion ) {
			case "nuevo": { this.accionNuevo(); break; }
			case "modificar": { this.accionModificar(); break; }
			default: throw new Error("Acci�n no conocida (" + accion + ")");
		}

	}
	catch (e) {
		alert(e.message);
	}

}

/*
 * M�todo gestionaPasswords()
 *
 *  Comprueba si se han cubierto correctamente las contrase�as
 *
 *  Par�metros: usuario: usuario cubierto
 *              password: contrase�a actual
 *              password1: contrase�a cubierta 1
 *              password2: contrase�a cubierta 2
 *              accion: acci�n original realizada
 *  Devuelve: 'true' si se ha cambiado la contrase�a; 'false' en caso contrario
 *  Lanza excepci�n si: las contrase�as no coinciden o est�n vac�as
 */
Edicion.prototype.gestionaPasswords = function (usuario, password, password1, password2, accion) {

	var bCambioPassword = false;

	if ( password == "" ) {
		if ( password1 == "" )  throw new Error("Debe cubrir la contrase�a de usuario");
		if ( password2 == "" )  throw new Error("Debe cubrir dos veces la contrase�a de usuario");
		if ( password1 != password2 )  throw new Error("Las contrase�as no coinciden");
		if ( usuario == password1 )  throw new Error("La contrase�a no ha de ser igual al c�digo de usuario. Seleccione otra, por favor");
		bCambioPassword = true;
	}
	else {
		if ( (password1 == "") && (password2 == "") ) {
			if ( accion == "nuevo" )  throw new Error("Debe cubrir la contrase�a de usuario");
			bCambioPassword = false;
		}
		else {
			if ( password1 == "" )  throw new Error("Para cambiar la contrase�a, debe cubrirla dos veces");
			if ( password2 == "" )  throw new Error("Para cambiar la contrase�a, debe cubrirla dos veces");
			if ( password1 != password2 )  throw new Error("Las contrase�as no coinciden");
			if ( usuario == password1 )  throw new Error("La contrase�a no ha de ser igual al c�digo de usuario. Seleccione otra, por favor");
			bCambioPassword = true;
		}
	}

	return bCambioPassword;

}


}