/*
 *  Funcionalidades a�adidas a la clase String de JavaScript.
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 20/12/2006
 */

/*
 * M�todo trim()
 *
 *  Elimina espacios al principio y al final de una cadena.
 *
 *  Par�metros: no tiene
 *  Devuelve: cadena sin espacios ni al principio ni al final
 */
String.prototype.trim = function () {

	cadena = this.toString();
	cadena = cadena.replace(/^\s+/g, "").replace(/\s+$/g, "");

	return cadena;

}

/*
 * M�todo codificar()
 *
 *  Codifica una cadena para su env�o HTTP tanto por GET como por POST.
 *
 *  Par�metros: no tiene
 *  Devuelve: cadena lista para su env�o por HTTP
 */
String.prototype.codificar = function () {

	cadena = this.toString();
	cadena = cadena.replace(/\\/g, "\\\\");
	cadena = escape(cadena);
	cadena = cadena.replace(/\+/g, "%2B");

	return cadena;

}

/*
 * M�todo descodificar()
 *
 *  Descodifica una cadena proviniente de una respuesta HTTP
 *
 *  Par�metros: no tiene
 *  Devuelve: cadena descodificada
 */
String.prototype.descodificar = function () {

	cadena = this.toString();
	cadena = cadena.replace(/%u20AC/g, "�");    // S�mbolo de euro

	return cadena;

}

/*
 * M�todo htmlEncode()
 *
 *  Realiza una condificaci�n HTML de la cadena
 *
 *  Par�metros: no tiene
 *  Devuelve: cadena codificada HTML
 */
String.prototype.htmlEncode = function () {

	cadena = this.toString();

	// Ampersand
	cadena = cadena.replace(/&/g, "&amp;");

	// Indicadores de tag
	cadena = cadena.replace(/</g, "&lt;");
	cadena = cadena.replace(/>/g, "&gt;");

	// Comillas simples y dobles
	cadena = cadena.replace(/\'/g, "&#39;");
	cadena = cadena.replace(/\"/g, "&quot;");

	// Volcales con tildes
	cadena = cadena.replace(/�/g, "&aacute;");
	cadena = cadena.replace(/�/g, "&eacute;");
	cadena = cadena.replace(/�/g, "&iacute;");
	cadena = cadena.replace(/�/g, "&oacute;");
	cadena = cadena.replace(/�/g, "&uacute;");
	cadena = cadena.replace(/�/g, "&Aacute;");
	cadena = cadena.replace(/�/g, "&Eacute;");
	cadena = cadena.replace(/�/g, "&Iacute;");
	cadena = cadena.replace(/�/g, "&Oacute;");
	cadena = cadena.replace(/�/g, "&Uacute;");

	// S�mbolos especiales
	cadena = cadena.replace(/�/g, "&euro;");

	// Espacios en blanco
	cadena = cadena.replace(/\s/g, "&nbsp;");

	return cadena;

}
