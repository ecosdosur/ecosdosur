/*
 * Clase Fecha.
 *
 *  Representa un fecha y sus formatos m�s �tiles.
 *
 *  IMPORTANTE: debe tener incluido antes el fichero 'String.js' (por el uso de String.trim())
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 14/12/2006
 */

/*
 * Constructor de la clase
 *
 *  Realiza las operaciones b�sicas para la generaci�n de una nueva fecha, tanto
 *   basada en el par�metro como sin �l (en ese caso, la fecha es de hoy)
 *
 *  Par�metros: strFecha: cadena que representa una fecha, tanto en formato ISO ('dd/mm/yyyy')
 *                        como SQL ('yyyy-mm-dd'). Si el par�metro est� vac�o, deja la fecha vac�a.
 *                        Si no se pasa este par�metro, se escoge el d�a de hoy.
 *  Lanza excepci�n si: el par�metro tiene un formato no esperado
 */
function Fecha (strFecha) {

	var dia, mes, a�o;

	if ( strFecha == null ) {
		// Si no se informa, cogemos la fecha de hoy
		var hoy = new Date();
		dia = hoy.getDate();
		mes = hoy.getMonth() + 1;
		a�o = hoy.getFullYear();

		if ( dia < 10 )  dia = "0" + dia;
		if ( mes < 10 )  mes = "0" + mes;
	}
	else {
		strFecha = strFecha.trim();
		if ( strFecha == "" ) {
			dia = mes = a�o = "";
		}
		else {
			if ( strFecha.length != 10 )  throw new Error('Fecha incorrecta: ' + strFecha);
			var arrayISO = strFecha.split("/");
			var arraySQL = strFecha.split("-");
			if ( arrayISO.length == 3 ) {
				dia = arrayISO[0]; mes = arrayISO[1]; a�o = arrayISO[2];
			}
			else if ( arraySQL.length == 3 ) {
				dia = arraySQL[2]; mes = arraySQL[1]; a�o = arraySQL[0];
			}
			else {
				throw new Error('Formato de fecha no reconocido: ' + strFecha);
			}
			if ( isNaN(dia) || isNaN(mes) || isNaN(a�o) ) {
				throw new Error('Formato de fecha no reconocido: ' + strFecha);
			}
		}
	}

	// Asignaci�n de atributos (pueden ser cambiados por llamada directa)
	this.dia = dia;
	this.mes = mes;
	this.a�o = a�o;

}

/*
 * M�todo getFechaSQL()
 *
 *  Obtiene la fecha representada en este objeto en formato SQL ('yyyy-mm-dd').
 *
 *  Par�metros: no tiene
 *  Devuelve: fecha en formato SQL o vac�a si el objeto fue creado con la cadena vac�a
 */
Fecha.prototype.getFechaSQL = function () {

	if ( this.dia == "" )  return "";
	else  return this.a�o + "-" + this.mes + "-" + this.dia;

}

/*
 * M�todo getFechaISO()
 *
 *  Obtiene la fecha representada en este objeto en formato SQL ('dd/mm/yyyy').
 *
 *  Par�metros: no tiene
 *  Devuelve: fecha en formato ISO o vac�a si el objeto fue creado con la cadena vac�a
 */
Fecha.prototype.getFechaISO = function () {

	if ( this.dia == "" )  return "";
	else  return this.dia + "/" + this.mes + "/" + this.a�o;

}