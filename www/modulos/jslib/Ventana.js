/*
 * Clase Ventana.
 *
 *  Representa una ventana
 *
 *  Autor: Pentared
 *  Última actualización: 29/12/2006
 */

/*
 * Constructor de la clase
 *
 *  Asigna los valores principales de la futura ventana. Después de ello, se ha de llamar
 *   al método mostrar() para lanzar la ventana.
 *
 *  Parámetros: url: dirección que se ha de cargar en la ventana
 *              ancho: ancho de la ventana. Por defecto es 800
 *              alto: alto de la ventana. Por defecto es 600
 *              esCentrada: booleano para indicar si se quiere centrar la ventana
 *              nombre: nombre de la ventana
 *  Son propiedades de la ventana, accesibles directamente, además de las anteriormente expuestas,
 *   las siguientes:
 *              top: posición de la parte superior de la ventana. Por defecto es 0. 
 *                   Se ignora si 'esCentrada' es 'true'
 *              left: posición de la parte izquierda de la ventana. Por defecto es 0. 
 *                    Se ignora si 'esCentrada' es 'true'
 *              resizable: booleano para indicar si se permite redimensionar. Por defecto es 'false'
 *              scrollbars: booleano para indicar si se quieren barras laterales. Por defecto es 'false'
 */
function Ventana (url, ancho, alto, esCentrada, nombre) {

	// Asignación de valores
	// - Dirección
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
 * Método mostrar()
 *
 *  Abre la ventana que representa este objeto a partir de los parámetros que se le
 *   han pasado.
 *
 *  Parámetros: no tiene
 *  Devuelve: 'true' si se ha abierto la ventana, 'false' en caso de que haya sido bloqueada
 *  Lanza excepción si: - algún parámetro erróneo
 *                      - error inesperado al llamar a window.open()
 */
Ventana.prototype.mostrar = function () {

	// Validación de parámetros
	// - Nombre
	var objRegExp = /\s/;
	if ( objRegExp.test(this.nombre) )  throw new Error("Nombre no válido: '" + this.nombre + "'");
	// - Dimensiones
	if ( isNaN(this.ancho) )  throw new Error("Ancho no válido: " + this.ancho);
	this.ancho = parseInt(this.ancho);
	if ( isNaN(this.alto) )  throw new Error("Alto no válido: " + this.alto);
	this.alto = parseInt(this.alto);
	// - Posicionamiento
	if ( this.esCentrada ) {
		this.top = parseInt((screen.availHeight - this.alto)/2);
		this.left = parseInt((screen.availWidth - this.ancho)/2);
	}
	else {
		if ( isNaN(this.top) )  throw new Error("Cota superior no válida: " + this.top);
		this.top = parseInt(this.top);
		if ( isNaN(this.left) )  throw new Error("Cota lateral izquierda no válida: " + this.left);
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