/*
 * Clase AJAXRequest.
 *
 *  Representa los pasos para realizar una petici�n as�ncrona a un servidor
 *   utilizando AJAX.
 *
 *  IMPORTANTE: debe tener incluido antes el fichero 'String.js' (por el uso de String.codificar())
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 12/12/2006
 */

/*
 * Constructor de la clase
 *
 *  Crea el objeto de la llamada, inicializando sus elementos.
 *
 *  Par�metros: no tiene
 *  Lanza excepci�n si: no es posible instanciar el objeto XMLHTTP
 */
function AJAXRequest () {

	// Atributo principal: el objeto de llamada as�ncrona
	this.objHttpRequest = false;

    // Objeto nativo XMLHttpRequest
    if ( window.XMLHttpRequest ) {
    	try {
			this.objHttpRequest = new XMLHttpRequest();
        } catch ( e ) {
			this.objHttpRequest = false;
        }
    // Versi�n ActiveX del objeto
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

	// Dem�s atributos: accesibles directamente o por sus respectivos m�todos
	this.url = "";
	this.parametros = "";
	this.funcionDeVuelta = null;

}

/*
 * M�todo setUrl()
 *
 *  Cambia la url del servidor. Puede se cambiada igualmente haciendo  objeto.url = url;
 *
 *  Par�metros: url: url del servidor al que se va a hacer la llamada as�ncrona
 */
AJAXRequest.prototype.setUrl = function (url) {

	this.url = url;

}

/*
 * M�todo setParametro()
 *
 *  A�ade un par�metro con su valor a la lista de par�metros a enviar.
 *
 *  Par�metros: parametro: nombre del par�metro
 *              valor: valor a enviar
 */
AJAXRequest.prototype.addParametro = function (parametro, valor) {

	if ( this.parametros != "" )  this.parametros += "&";
	this.parametros += parametro + "=" + valor.toString().codificar();

}

/*
 * M�todo setFuncionDeVuelta()
 *
 *  Asigna la funci�n que se ejecutar� en los sucesivos cambios de estado del objeto
 *   de la llamada as�ncrona. Tambi�n se puede asignar como  objeto.funcionDeVuelta = funcion
 *
 *  Par�metros: funcion: funci�n de vuelta
 */
AJAXRequest.prototype.setFuncionDeVuelta = function (funcion) {

	this.funcionDeVuelta = funcion;

}

/*
 * M�todo enviar()
 *
 *  Realiza la llamada (mediante POST) de los datos representados en este objeto.
 *
 *  Par�metros: no tiene
 *  Lanza excepci�n si: falta alg�n par�metro clave
 */
AJAXRequest.prototype.enviar = function () {

	// URL
	if ( this.url == "" )  throw new Error("AJAXRequest: URL vac�a");

	// Funci�n de vuelta
	if ( this.funcionDeVuelta == null )  throw new Error("AJAXRequest: no hay funci�n de vuelta");
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
 * M�todo finCarga()
 *
 *  Realiza la comprobaci�n de fin de carga de la respuesta
 *
 *  Par�metros: no tiene
 *  Devuelve: 'true' si ha finalizado; 'false' en caso contrario
 */
AJAXRequest.prototype.finCarga = function () {

	return (this.objHttpRequest.readyState == 4);

}

/*
 * M�todo hayErrorHTTP()
 *
 *  Comprueba si se ha producido alg�n error en la comunicaci�n HTTP
 *
 *  Par�metros: no tiene
 *  Devuelve: 'true' si ha habido error; 'false' en caso contrario
 */
AJAXRequest.prototype.hayErrorHTTP = function () {

	return (this.objHttpRequest.status != 200);

}

/*
 * M�todo getErrorHTTP()
 *
 *  Obtiene la cadena con el mensaje de error HTTP si �ste se ha producido
 *
 *  Par�metros: no tiene
 *  Devuelve: cadena que representa el error HTTP o cadena vac�a si no �ste no se ha producido
 */
AJAXRequest.prototype.getErrorHTTP = function () {

	return (this.hayErrorHTTP() ? this.objHttpRequest.statusText : "");

}

/*
 * M�todo getRespuestaJSON()
 *
 *  Objeto JavaScript con la respuesta de la llamada as�ncrona
 *
 *  Par�metros: no tiene
 *  Devuelve: objeto JavaScript de la respuesta de la llamada as�ncrona
 */
AJAXRequest.prototype.getRespuestaJSON = function () {

	return eval("(" + this.objHttpRequest.responseText + ")");

}

/*
 * M�todo getRespuestaXML()
 *
 *  Objeto XML con la respuesta de la llamada as�ncrona
 *
 *  Par�metros: no tiene
 *  Devuelve: objeto XML de la respuesta de la llamada as�ncrona
 */
AJAXRequest.prototype.getRespuestaXML = function () {

	return this.objHttpRequest.responseXML;

}