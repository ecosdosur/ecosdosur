/*
 * Clase AJAXRequest.
 *
 *  Representa los pasos para realizar una petición asíncrona a un servidor
 *   utilizando AJAX.
 *
 *  IMPORTANTE: debe tener incluido antes el fichero 'String.js' (por el uso de String.codificar())
 *
 *  Autor: Pentared
 *  Última actualización: 12/12/2006
 */

/*
 * Constructor de la clase
 *
 *  Crea el objeto de la llamada, inicializando sus elementos.
 *
 *  Parámetros: no tiene
 *  Lanza excepción si: no es posible instanciar el objeto XMLHTTP
 */
function AJAXRequest () {

	// Atributo principal: el objeto de llamada asíncrona
	this.objHttpRequest = false;

    // Objeto nativo XMLHttpRequest
    if ( window.XMLHttpRequest ) {
    	try {
			this.objHttpRequest = new XMLHttpRequest();
        } catch ( e ) {
			this.objHttpRequest = false;
        }
    // Versión ActiveX del objeto
    } else if ( window.ActiveXObject ) {
       	try {
        	this.objHttpRequest = new ActiveXObject("Msxml2.XMLHTTP");
      	} catch ( e ) {
        	try {
          		this.objHttpRequest = new ActiveXObject("Microsoft.XMLHTTP");
        	} catch ( e ) {
          		this.objHttpRequest = false;
        	}
		}
    }

	// Demás atributos: accesibles directamente o por sus respectivos métodos
	this.url = "";
	this.parametros = "";
	this.funcionDeVuelta = null;

}

/*
 * Método setUrl()
 *
 *  Cambia la url del servidor. Puede se cambiada igualmente haciendo  objeto.url = url;
 *
 *  Parámetros: url: url del servidor al que se va a hacer la llamada asíncrona
 */
AJAXRequest.prototype.setUrl = function (url) {

	this.url = url;

}

/*
 * Método setParametro()
 *
 *  Añade un parámetro con su valor a la lista de parámetros a enviar.
 *
 *  Parámetros: parametro: nombre del parámetro
 *              valor: valor a enviar
 */
AJAXRequest.prototype.addParametro = function (parametro, valor) {

	if ( this.parametros != "" )  this.parametros += "&";
	this.parametros += parametro + "=" + valor.toString().codificar();

}

/*
 * Método setFuncionDeVuelta()
 *
 *  Asigna la función que se ejecutará en los sucesivos cambios de estado del objeto
 *   de la llamada asíncrona. También se puede asignar como  objeto.funcionDeVuelta = funcion
 *
 *  Parámetros: funcion: función de vuelta
 */
AJAXRequest.prototype.setFuncionDeVuelta = function (funcion) {

	this.funcionDeVuelta = funcion;

}

/*
 * Método enviar()
 *
 *  Realiza la llamada (mediante POST) de los datos representados en este objeto.
 *
 *  Parámetros: no tiene
 *  Lanza excepción si: falta algún parámetro clave
 */
AJAXRequest.prototype.enviar = function () {

	// URL
	if ( this.url == "" )  throw new Error("AJAXRequest: URL vacía");

	// Función de vuelta
	if ( this.funcionDeVuelta == null )  throw new Error("AJAXRequest: no hay función de vuelta");
	//this.objHttpRequest.onreadystatechange = function () { 
		//this.preVuelta(this.funcionDeVuelta);
		//if ( this.objHttpRequest.readyState == 4 )  eval(this.funcionDeVuelta);
	//};
	//this.objHttpRequest.onreadystatechange = this.funcionDeVuelta;

	this.objHttpRequest.open("POST", this.url, true);
	this.objHttpRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	this.objHttpRequest.setRequestHeader("Content-length", this.parametros.length);
	this.objHttpRequest.setRequestHeader("Connection", "close");
	this.objHttpRequest.send(this.parametros);
	this.objHttpRequest.onreadystatechange = this.funcionDeVuelta;

}

/*
 * Método finCarga()
 *
 *  Realiza la comprobación de fin de carga de la respuesta
 *
 *  Parámetros: no tiene
 *  Devuelve: 'true' si ha finalizado; 'false' en caso contrario
 */
AJAXRequest.prototype.finCarga = function () {

	return (this.objHttpRequest.readyState == 4);

}

/*
 * Método hayErrorHTTP()
 *
 *  Comprueba si se ha producido algún error en la comunicación HTTP
 *
 *  Parámetros: no tiene
 *  Devuelve: 'true' si ha habido error; 'false' en caso contrario
 */
AJAXRequest.prototype.hayErrorHTTP = function () {

	return (this.objHttpRequest.status != 200);

}

/*
 * Método getErrorHTTP()
 *
 *  Obtiene la cadena con el mensaje de error HTTP si éste se ha producido
 *
 *  Parámetros: no tiene
 *  Devuelve: cadena que representa el error HTTP o cadena vacía si no éste no se ha producido
 */
AJAXRequest.prototype.getErrorHTTP = function () {

	return (this.hayErrorHTTP() ? this.objHttpRequest.statusText : "");

}

/*
 * Método getRespuestaJSON()
 *
 *  Objeto JavaScript con la respuesta de la llamada asíncrona
 *
 *  Parámetros: no tiene
 *  Devuelve: objeto JavaScript de la respuesta de la llamada asíncrona
 */
AJAXRequest.prototype.getRespuestaJSON = function () {

	return eval("(" + this.objHttpRequest.responseText + ")");

}

/*
 * Método getRespuestaXML()
 *
 *  Objeto XML con la respuesta de la llamada asíncrona
 *
 *  Parámetros: no tiene
 *  Devuelve: objeto XML de la respuesta de la llamada asíncrona
 */
AJAXRequest.prototype.getRespuestaXML = function () {

	return this.objHttpRequest.responseXML;

}