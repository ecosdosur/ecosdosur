/*
 *  Funcionalidades específicas de la pantalla de usuarios del sistema
 *
 *  Autor: Pentared
 *  Última actualización: 20/12/2006
 */

// Control de código: ha de estar incluido el fichero 'Edicion.js'
if ( typeof Edicion != "undefined" ) {


/*
 * Método nuevo()
 *
 *  Realiza las operaciones pertinentes tras pulsar el botón 'Nuevo'. Sobrepone al que hay en 'Edicion.js'
 *
 *  Parámetros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.nuevo = function () {

	this.accionUsuarios("nuevo");

}

/*
 * Método nuevo()
 *
 *  Realiza las operaciones pertinentes tras pulsar el botón 'Modificar'. Sobrepone al que hay en 'Edicion.js'
 *
 *  Parámetros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.modificar = function () {

	this.accionUsuarios("modificar");

}

/*
 * Método accionUsuarios()
 *
 *  Realiza las operaciones pertinentes tras pulsar en 'Nuevo' o 'Modificar': obtener los
 *   valores encriptados del usuario y la contraseña
 *
 *  Parámetros: accion: acción seleccionada ('nuevo' o 'modificar')
 *  Devuelve: nada
 */
Edicion.prototype.accionUsuarios = function (accion) {

	// Comprobación previa de campos
	var usuario = document.getElementById("USUARIO").value;
	var nombre = document.getElementById("DESCRIPCION").value;
	var password = document.getElementById("PASSWORD").value;
	var password1 = document.getElementById("PASSWORD_clara1").value;
	var password2 = document.getElementById("PASSWORD_clara2").value;

	try {
		// Control de parámetros
		if ( usuario == "" )  throw new Error("Debe cubrir el código de usuario");
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
 * Método vueltaAccionUsuarios()
 *
 *  Comprueba que se han obtenido con éxito los valores requeridos por accionUsuarios()
 *
 *  Parámetros: objAjax: objeto AJAXRequest con el resultado de la consulta
 *              accion: acción original
 *              bCambioPassword: 'true' si se ha cambiado la contraseña; 'false' en caso contrario
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
	
		// Acción original
		switch ( accion ) {
			case "nuevo": { this.accionNuevo(); break; }
			case "modificar": { this.accionModificar(); break; }
			default: throw new Error("Acción no conocida (" + accion + ")");
		}

	}
	catch (e) {
		alert(e.message);
	}

}

/*
 * Método gestionaPasswords()
 *
 *  Comprueba si se han cubierto correctamente las contraseñas
 *
 *  Parámetros: usuario: usuario cubierto
 *              password: contraseña actual
 *              password1: contraseña cubierta 1
 *              password2: contraseña cubierta 2
 *              accion: acción original realizada
 *  Devuelve: 'true' si se ha cambiado la contraseña; 'false' en caso contrario
 *  Lanza excepción si: las contraseñas no coinciden o están vacías
 */
Edicion.prototype.gestionaPasswords = function (usuario, password, password1, password2, accion) {

	var bCambioPassword = false;

	if ( password == "" ) {
		if ( password1 == "" )  throw new Error("Debe cubrir la contraseña de usuario");
		if ( password2 == "" )  throw new Error("Debe cubrir dos veces la contraseña de usuario");
		if ( password1 != password2 )  throw new Error("Las contraseñas no coinciden");
		if ( usuario == password1 )  throw new Error("La contraseña no ha de ser igual al código de usuario. Seleccione otra, por favor");
		bCambioPassword = true;
	}
	else {
		if ( (password1 == "") && (password2 == "") ) {
			if ( accion == "nuevo" )  throw new Error("Debe cubrir la contraseña de usuario");
			bCambioPassword = false;
		}
		else {
			if ( password1 == "" )  throw new Error("Para cambiar la contraseña, debe cubrirla dos veces");
			if ( password2 == "" )  throw new Error("Para cambiar la contraseña, debe cubrirla dos veces");
			if ( password1 != password2 )  throw new Error("Las contraseñas no coinciden");
			if ( usuario == password1 )  throw new Error("La contraseña no ha de ser igual al código de usuario. Seleccione otra, por favor");
			bCambioPassword = true;
		}
	}

	return bCambioPassword;

}


}