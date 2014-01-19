/*
 * Clase Fecha.
 *
 *  Representa un fecha y sus formatos más útiles.
 *
 *  IMPORTANTE: debe tener incluido antes el fichero 'String.js' (por el uso de String.trim())
 *
 *  Autor: Pentared
 *  Última actualización: 14/12/2006
 */

/*
 * Constructor de la clase
 *
 *  Realiza las operaciones básicas para la generación de una nueva fecha, tanto
 *   basada en el parámetro como sin él (en ese caso, la fecha es de hoy)
 *
 *  Parámetros: strFecha: cadena que representa una fecha, tanto en formato ISO ('dd/mm/yyyy')
 *                        como SQL ('yyyy-mm-dd'). Si el parámetro está vacío, deja la fecha vacía.
 *                        Si no se pasa este parámetro, se escoge el día de hoy.
 *  Lanza excepción si: el parámetro tiene un formato no esperado
 */
function Fecha (strFecha) {

	var dia, mes, año;

	if ( strFecha == null ) {
		// Si no se informa, cogemos la fecha de hoy
		var hoy = new Date();
		dia = hoy.getDate();
		mes = hoy.getMonth() + 1;
		año = hoy.getFullYear();

		if ( dia < 10 )  dia = "0" + dia;
		if ( mes < 10 )  mes = "0" + mes;
	}
	else {
		strFecha = strFecha.trim();
		if ( strFecha == "" ) {
			dia = mes = año = "";
		}
		else {
			if ( strFecha.length != 10 )  throw new Error('Fecha incorrecta: ' + strFecha);
			var arrayISO = strFecha.split("/");
			var arraySQL = strFecha.split("-");
			if ( arrayISO.length == 3 ) {
				dia = arrayISO[0]; mes = arrayISO[1]; año = arrayISO[2];
			}
			else if ( arraySQL.length == 3 ) {
				dia = arraySQL[2]; mes = arraySQL[1]; año = arraySQL[0];
			}
			else {
				throw new Error('Formato de fecha no reconocido: ' + strFecha);
			}
			if ( isNaN(dia) || isNaN(mes) || isNaN(año) ) {
				throw new Error('Formato de fecha no reconocido: ' + strFecha);
			}
		}
	}

	// Asignación de atributos (pueden ser cambiados por llamada directa)
	this.dia = dia;
	this.mes = mes;
	this.año = año;

}

/*
 * Método getFechaSQL()
 *
 *  Obtiene la fecha representada en este objeto en formato SQL ('yyyy-mm-dd').
 *
 *  Parámetros: no tiene
 *  Devuelve: fecha en formato SQL o vacía si el objeto fue creado con la cadena vacía
 */
Fecha.prototype.getFechaSQL = function () {

	if ( this.dia == "" )  return "";
	else  return this.año + "-" + this.mes + "-" + this.dia;

}

/*
 * Método getFechaISO()
 *
 *  Obtiene la fecha representada en este objeto en formato SQL ('dd/mm/yyyy').
 *
 *  Parámetros: no tiene
 *  Devuelve: fecha en formato ISO o vacía si el objeto fue creado con la cadena vacía
 */
Fecha.prototype.getFechaISO = function () {

	if ( this.dia == "" )  return "";
	else  return this.dia + "/" + this.mes + "/" + this.año;

}