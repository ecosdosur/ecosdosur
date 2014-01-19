/*
 *  Funcionalidades espec�ficas de la pantalla de usuarios del Ecos do Sur
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 05/01/2007
 */

// Control de c�digo: ha de estar incluido el fichero 'Edicion.js'
if ( typeof Edicion != "undefined" ) {


Edicion.prototype.irAPestana = function (numPestana) {

	// N�mero total de pesta�as
	var numPestanas = 9;

	// Hacemos no visibles todas
	var objDiv = null;
	var objPestana = null;
	for ( var k = 1; k <= numPestanas; k++ ) {
		objPestana = document.getElementById("pestana" + k);
		objDiv = document.getElementById("panelPestana" + k);
		if ( objDiv ) {
			objPestana.className = "clPestanaInactiva";
			objDiv.style.display = "none";
		}
	}

	// Hacemos visible la seleccionada
	objPestana = document.getElementById("pestana" + numPestana);
	objDiv = document.getElementById("panelPestana" + numPestana);
	if ( objDiv ) {
		objPestana.className = "clPestanaActiva";
		objDiv.style.display = "";
	}


}


}