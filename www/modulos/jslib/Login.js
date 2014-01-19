/*
 * Clase Login.
 *
 *  Representa las operaciones para hacer login en el sistema.
 *
 *  IMPORTANTE: debe tener incluido antes los siguientes ficheros: 'String.js', 'Ventana.js' 
 *   y 'AJAXRequest.js'.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 16/01/2007
 */

/*
 * Constructor de la clase
 *
 *  Realiza las operaciones b�sicas para la gesti�n del login: inicializaci�n de variables
 *
 *  Par�metros: libreriaSistema: librer�a del sistema
 *              clave: clave para encriptaci�n del usuario y contrase�a
 */
function Login (libreriaSistema, clave) {
	
	this.libreriaSistema = libreriaSistema;
	this.clave = clave;

	this.usuario = this.password = "";
	
}


/*
 * M�todo inicio()
 *
 *  Realiza las acciones pertinentes tras la carga de la p�gina
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Login.prototype.inicio = function () {

	// Foco sobre el input de usuario
	document.getElementById("usuario").focus();

}


/*
 * M�todo accionKeyDown()
 *
 *  Funci�n que se ejecuta al pulsar una tecla en el objeto dado
 *
 *  Par�metros: objeto: objeto sobre el que se puls� la tecla
 *              evt: ebjeto del evento
 *  Devuelve: nada
 */
Login.prototype.accionKeyDown = function (objeto, evt) {

	// Si se ha pulsado INTRO y est� cubierta la contrase�a, entramos
	if ( (evt.keyCode == 13) && (objeto.value != "") )  this.entrar();

}


/*
 * M�todo entrar()
 *
 *  Funci�n que se ejecuta al pulsar bot�n 'Entrar'.
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 */
Login.prototype.entrar = function () {

	try {
		// Control de par�metros
		var usuario = document.getElementById("usuario").value.trim();
		var password = document.getElementById("passwd").value.trim();
		if ( (this.libreriaSistema != "") && (this.libreriaSistema.indexOf(".") == -1) ) {
			this.libreriaSistema += ".";
		}

		//if ( this.libreriaSistema == "" )  throw new Error("Librer�a del sistema mal configurada");
		if ( this.clave == "" )  throw new Error("Clave mal configurada");
		if ( usuario == "" )  throw new Error("Por favor, rellene el usuario");
		if ( password == "" )  throw new Error("Por favor, rellene la contrase�a");

		this.usuario = usuario;
		this.password = password;

		// B�squeda de contrase�a
		this.getPasswordEncriptada();

		// Deshabilitaci�n
		document.getElementById("usuario").readOnly = true;
		document.getElementById("passwd").readOnly = true;
		document.getElementById("btnLogin").disabled = true;

	}
	catch (e) {
		alert(e.message);
	}

}

/*
 * M�todo getPasswordEncriptada()
 *
 *  Realiza la llamada para obtener la contrase�a encriptada
 *
 *  Par�metros: no tiene
 *  Devuelve: nada
 *  Lanza excepci�n si: - error al manejar la llamada as�ncrona al servidor
 */
Login.prototype.getPasswordEncriptada = function () {

	var expresion = "crypt('" + this.password + "', '" + this.clave + "')";

	var objAjax = new AJAXRequest();
	objAjax.setUrl("servidor.php");
	objAjax.addParametro("tipoServidor", "Evalua");
	objAjax.addParametro("numAcciones", "1");
	objAjax.addParametro("expresion", expresion);
	
	var objThis = this;
	objAjax.setFuncionDeVuelta(function () { objThis.vueltaGetPasswordEncriptada(objAjax); });
	objAjax.enviar();
	
}

/*
 * M�todo vueltaGetPasswordEncriptada()
 *
 *  Obtiene la contrase�a encriptada y procede a la validaci�n del usuario
 *
 *  Par�metros: objAjax: objeto AJAXRequest
 *  Devuelve: nada
 */
Login.prototype.vueltaGetPasswordEncriptada = function (objAjax) {

	try {
		if ( !objAjax.finCarga() )  return;
		if ( objAjax.hayErrorHTTP() )  throw new Error(objAjax.getErrorHTTP());

		//alert(objAjax.objHttpRequest.responseText);
		var objResultado = objAjax.getRespuestaJSON().resultado_consulta;
		//alert(objetoRespuesta.contenido);
		
		// Control de error:
		if ( objResultado.error != "" )  throw new Error(objResultado.error);

		// Validaci�n
		//alert(objResultado.contenido);
		this.validar(objResultado.contenido);
	
	}
	catch (e) {
		// Habilitaci�n
		document.getElementById("usuario").readOnly = false;
		document.getElementById("passwd").readOnly = false;
		document.getElementById("btnLogin").disabled = false;
		// Informe de error
		alert(e.message);
	}

}

/*
 * M�todo validar()
 *
 *  Realiza la llamada para validar el usuario y contrase�a introducidos
 *
 *  Par�metros: passwordEncriptada: contrase�a encriptada
 *  Devuelve: nada
 *  Lanza excepci�n si: - error en la llamada as�ncrona al servidor
 */
Login.prototype.validar = function (passwordEncriptada) {

	var consulta = "SELECT CUSUARIO FROM {$libreria}usuarios WHERE USUARIO='{$usuario}' AND PASSWORD='{$password}'";
	
	var objAjax = new AJAXRequest();
	objAjax.setUrl("servidor.php");
	objAjax.addParametro("tipoServidor", "Consulta");
	objAjax.addParametro("numAcciones", "1");
	objAjax.addParametro("sql", consulta);
	objAjax.addParametro("plantilla", "{$CUSUARIO}");
	//alert(consulta);
	objAjax.addParametro("libreria", this.libreriaSistema);
	objAjax.addParametro("usuario", this.usuario);
	objAjax.addParametro("password", passwordEncriptada);
	var objThis = this;
	objAjax.setFuncionDeVuelta(function () { objThis.vueltaValidar(objAjax); });
	objAjax.enviar();
	
}

/*
 * M�todo vueltaValidar()
 *
 *  Comprueba que la validaci�n se ha realizado correctamente
 *
 *  Par�metros: objAjax: objeto AJAXRequest
 *  Devuelve: nada
 */
Login.prototype.vueltaValidar = function (objAjax) {

	try {
		if ( !objAjax.finCarga() )  return;
		if ( objAjax.hayErrorHTTP() )  throw new Error(objAjax.getErrorHTTP());

		//alert(objAjax.objHttpRequest.responseText);
		var objResultado = objAjax.getRespuestaJSON().resultado_consulta;
		//alert(objetoRespuesta.contenido);
		
		// Control de error:
		if ( objResultado.error != "" )  throw new Error(objResultado.error);

		if ( objResultado.numResultados == 0 )  throw new Error("Usuario y/o contrase�a incorrectos");

		// Acceder con el c�digo de usuario encriptado
		this.acceder(objResultado.contenido);
	
	}
	catch (e) {
		// Informe de error
		alert(e.message);
	}

	// Habilitaci�n
	document.getElementById("usuario").value = "";
	document.getElementById("passwd").value = "";
	document.getElementById("usuario").readOnly = false;
	document.getElementById("passwd").readOnly = false;
	document.getElementById("btnLogin").disabled = false;
	//document.getElementById("usuario").focus();

}

/*
 * M�todo acceder()
 *
 *  Abre la ventana con el acceso a la aplicaci�n
 *
 *  Par�metros: usuarioEncriptado: usuario encriptado
 *  Devuelve: nada
 */
Login.prototype.acceder = function (usuarioEncriptado) {

	var url = "marco.php?usuario=" + usuarioEncriptado;
	var ventana = new Ventana(url, 950, 700, true);
	try {
		if ( !ventana.mostrar() )  throw new Error("Tiene activado el bloqueador de ventanas emergentes. Por favor, desact�velo para este sitio web");
	}
	catch (e) {
		alert(e.message);
	}

}