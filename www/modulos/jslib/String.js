/*
 *  Funcionalidades añadidas a la clase String de JavaScript.
 *
 *  Autor: Pentared
 *  Última actualización: 20/12/2006
 */

/*
 * Método trim()
 *
 *  Elimina espacios al principio y al final de una cadena.
 *
 *  Parámetros: no tiene
 *  Devuelve: cadena sin espacios ni al principio ni al final
 */
String.prototype.trim = function () {

	cadena = this.toString();
	cadena = cadena.replace(/^\s+/g, "").replace(/\s+$/g, "");

	return cadena;

}

/*
 * Método codificar()
 *
 *  Codifica una cadena para su envío HTTP tanto por GET como por POST.
 *
 *  Parámetros: no tiene
 *  Devuelve: cadena lista para su envío por HTTP
 */
String.prototype.codificar = function () {

	cadena = this.toString();
	cadena = cadena.replace(/\\/g, "\\\\");
	cadena = escape(cadena);
	cadena = cadena.replace(/\+/g, "%2B");

	return cadena;

}

/*
 * Método descodificar()
 *
 *  Descodifica una cadena proviniente de una respuesta HTTP
 *
 *  Parámetros: no tiene
 *  Devuelve: cadena descodificada
 */
String.prototype.descodificar = function () {

	cadena = this.toString();
	cadena = cadena.replace(/%u20AC/g, "€");    // Símbolo de euro

	return cadena;

}

/*
 * Método htmlEncode()
 *
 *  Realiza una condificación HTML de la cadena
 *
 *  Parámetros: no tiene
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
	cadena = cadena.replace(/á/g, "&aacute;");
	cadena = cadena.replace(/é/g, "&eacute;");
	cadena = cadena.replace(/í/g, "&iacute;");
	cadena = cadena.replace(/ó/g, "&oacute;");
	cadena = cadena.replace(/ú/g, "&uacute;");
	cadena = cadena.replace(/Á/g, "&Aacute;");
	cadena = cadena.replace(/É/g, "&Eacute;");
	cadena = cadena.replace(/Í/g, "&Iacute;");
	cadena = cadena.replace(/Ó/g, "&Oacute;");
	cadena = cadena.replace(/Ú/g, "&Uacute;");

	// Símbolos especiales
	cadena = cadena.replace(/€/g, "&euro;");

	// Espacios en blanco
	cadena = cadena.replace(/\s/g, "&nbsp;");

	return cadena;

}
