/*
 * Clase Login.
 *
 *  Representa las operaciones para hacer login en el sistema.
 *
 *  IMPORTANTE: debe tener incluido antes los siguientes ficheros: 'String.js', 'Ventana.js' 
 *   y 'AJAXRequest.js'.
 *
 *  Autor: Pentared
 *  Última actualización: 16/01/2007
 */

/*
 * Constructor de la clase
 *
 *  Realiza las operaciones básicas para la gestión del login: inicialización de variables
 *
 *  Parámetros: libreriaSistema: librería del sistema
 *              clave: clave para encriptación del usuario y contraseña
 */
function Login (libreriaSistema, clave) {
	
	this.libreriaSistema = libreriaSistema;
	this.clave = clave;

	this.usuario = this.password = "";
	
}


/*
 * Método inicio()
 *
 *  Realiza las acciones pertinentes tras la carga de la página
 *
 *  Parámetros: no tiene
 *  Devuelve: nada
 */
Login.prototype.inicio = function () {

	// Foco sobre el input de usuario
	document.getElementById("usuario").focus();

}


/*
 * Método accionKeyDown()
 *
 *  Función que se ejecuta al pulsar una tecla en el objeto dado
 *
 *  Parámetros: objeto: objeto sobre el que se pulsó la tecla
 *              evt: ebjeto del evento
 *  Devuelve: nada
 */
Login.prototype.accionKeyDown = function (objeto, evt) {

	// Si se ha pulsado INTRO y está cubierta la contraseña, entramos
	if ( (evt.keyCode == 13) && (objeto.value != "") )  this.entrar();

}


/*
 * Método entrar()
 *
 *  Función que se ejecuta al pulsar botón 'Entrar'.
 *
 *  Parámetros: no tiene
 *  Devuelve: nada
 */
Login.prototype.entrar = function () {

	try {
		// Control de parámetros
		var usuario = document.getElementById("usuario").value.trim();
		var password = document.getElementById("passwd").value.trim();
		if ( (this.libreriaSistema != "") && (this.libreriaSistema.indexOf(".") == -1) ) {
			this.libreriaSistema += ".";
		}

		//if ( this.libreriaSistema == "" )  throw new Error("Librería del sistema mal configurada");
		if ( this.clave == "" )  throw new Error("Clave mal configurada");
		if ( usuario == "" )  throw new Error("Por favor, rellene el usuario");
		if ( password == "" )  throw new Error("Por favor, rellene la contraseña");

		this.usuario = usuario;
		this.password = password;

		// Búsqueda de contraseña
		this.getPasswordEncriptada();

		// Deshabilitación
		document.getElementById("usuario").readOnly = true;
		document.getElementById("passwd").readOnly = true;
		document.getElementById("btnLogin").disabled = true;

	}
	catch (e) {
		alert(e.message);
	}

}

/*
 * Método getPasswordEncriptada()
 *
 *  Realiza la llamada para obtener la contraseña encriptada
 *
 *  Parámetros: no tiene
 *  Devuelve: nada
 *  Lanza excepción si: - error al manejar la llamada asíncrona al servidor
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
 * Método vueltaGetPasswordEncriptada()
 *
 *  Obtiene la contraseña encriptada y procede a la validación del usuario
 *
 *  Parámetros: objAjax: objeto AJAXRequest
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

		// Validación
		//alert(objResultado.contenido);
		this.validar(objResultado.contenido);
	
	}
	catch (e) {
		// Habilitación
		document.getElementById("usuario").readOnly = false;
		document.getElementById("passwd").readOnly = false;
		document.getElementById("btnLogin").disabled = false;
		// Informe de error
		alert(e.message);
	}

}

/*
 * Método validar()
 *
 *  Realiza la llamada para validar el usuario y contraseña introducidos
 *
 *  Parámetros: passwordEncriptada: contraseña encriptada
 *  Devuelve: nada
 *  Lanza excepción si: - error en la llamada asíncrona al servidor
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
 * Método vueltaValidar()
 *
 *  Comprueba que la validación se ha realizado correctamente
 *
 *  Parámetros: objAjax: objeto AJAXRequest
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

		if ( objResultado.numResultados == 0 )  throw new Error("Usuario y/o contraseña incorrectos");

		// Acceder con el código de usuario encriptado
		this.acceder(objResultado.contenido);
	
	}
	catch (e) {
		// Informe de error
		alert(e.message);
	}

	// Habilitación
	document.getElementById("usuario").value = "";
	document.getElementById("passwd").value = "";
	document.getElementById("usuario").readOnly = false;
	document.getElementById("passwd").readOnly = false;
	document.getElementById("btnLogin").disabled = false;
	//document.getElementById("usuario").focus();

}

/*
 * Método acceder()
 *
 *  Abre la ventana con el acceso a la aplicación
 *
 *  Parámetros: usuarioEncriptado: usuario encriptado
 *  Devuelve: nada
 */
Login.prototype.acceder = function (usuarioEncriptado) {

	var url = "marco.php?usuario=" + usuarioEncriptado;
	var ventana = new Ventana(url, 950, 700, true);
	try {
		if ( !ventana.mostrar() )  throw new Error("Tiene activado el bloqueador de ventanas emergentes. Por favor, desactívelo para este sitio web");
	}
	catch (e) {
		alert(e.message);
	}

}