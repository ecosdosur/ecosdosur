/*
 * Clase Ventana.
 *
 *  Representa una ventana
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 29/12/2006
 */

/*
 * Constructor de la clase
 *
 *  Asigna los valores principales de la futura ventana. Despu�s de ello, se ha de llamar
 *   al m�todo mostrar() para lanzar la ventana.
 *
 *  Par�metros: url: direcci�n que se ha de cargar en la ventana
 *              ancho: ancho de la ventana. Por defecto es 800
 *              alto: alto de la ventana. Por defecto es 600
 *              esCentrada: booleano para indicar si se quiere centrar la ventana
 *              nombre: nombre de la ventana
 *  Son propiedades de la ventana, accesibles directamente, adem�s de las anteriormente expuestas,
 *   las siguientes:
 *              top: posici�n de la parte superior de la ventana. Por defecto es 0. 
 *                   Se ignora si 'esCentrada' es 'true'
 *              left: posici�n de la parte izquierda de la ventana. Por defecto es 0. 
 *                    Se ignora si 'esCentrada' es 'true'
 *              resizable: booleano para indicar si se permite redimensionar. Por defecto es 'false'
 *              scrollbars: booleano para indicar si se quieren barras laterales. Por defecto es 'false'
 */
function Ventana (url, ancho, alto, esCentrada, nombre) {

	// Asignaci�n de valores
	// - Direcci�n
	this.url = url;
	// - Dimensiones
	if ( ancho )  this.ancho = ancho;
	else  this.ancho = 800;
	if ( alto )  this.alto = alto;
	else  this.alto = 600;
	// - Posicionamiento
	this.esCentrada = esCentrada;
	this.top = this.left = 0;
	// - Opciones
	this.resizable = false;
	this.scrollbars = false;
	// - Nombre
	if ( nombre )  this.nombre = nombre;
	else  this.nombre = "";

}

/*
 * M�todo mostrar()
 *
 *  Abre la ventana que representa este objeto a partir de los par�metros que se le
 *   han pasado.
 *
 *  Par�metros: no tiene
 *  Devuelve: 'true' si se ha abierto la ventana, 'false' en caso de que haya sido bloqueada
 *  Lanza excepci�n si: - alg�n par�metro err�neo
 *                      - error inesperado al llamar a window.open()
 */
Ventana.prototype.mostrar = function () {

	// Validaci�n de par�metros
	// - Nombre
	var objRegExp = /\s/;
	if ( objRegExp.test(this.nombre) )  throw new Error("Nombre no v�lido: '" + this.nombre + "'");
	// - Dimensiones
	if ( isNaN(this.ancho) )  throw new Error("Ancho no v�lido: " + this.ancho);
	this.ancho = parseInt(this.ancho);
	if ( isNaN(this.alto) )  throw new Error("Alto no v�lido: " + this.alto);
	this.alto = parseInt(this.alto);
	// - Posicionamiento
	if ( this.esCentrada ) {
		this.top = parseInt((screen.availHeight - this.alto)/2);
		this.left = parseInt((screen.availWidth - this.ancho)/2);
	}
	else {
		if ( isNaN(this.top) )  throw new Error("Cota superior no v�lida: " + this.top);
		this.top = parseInt(this.top);
		if ( isNaN(this.left) )  throw new Error("Cota lateral izquierda no v�lida: " + this.left);
		this.left = parseInt(this.left);
	}

	// Lanzamiento de la ventana
	var resVentana = false;
	try {
		var opciones = "top=" + this.top + ",left=" + this.left + ",width=" + this.ancho + ",height=" + this.alto;
		opciones += (this.resizable ? ",resizable=yes" : "");
		opciones += (this.scrollbars ? ",scrollbars=yes" : "");
		resVentana = window.open(this.url, this.nombre, opciones);
		resVentana = !((resVentana == null) || (typeof(resVentana) == "undefined"));
	}
	catch (e) {
		throw new Error("Error inesperado: " + e.message);
	}

	// Valor retornado
	return resVentana;

}