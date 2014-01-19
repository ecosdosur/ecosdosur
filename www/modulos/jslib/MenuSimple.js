/*
 * Clase MenuSimple.
 *
 *  Representa un men� simple con un solo nivel de submen�s.
 *
 *  IMPORTANTE: debe tener incluido antes los siguientes ficheros: 'DivEmergente.js'
 *
 *  Autor: Pentared
 *  �ltima actualizaci�n: 07/02/2007
 */


/*
 * Constructor de la clase
 *
 *  Realiza las operaciones b�sicas para preparar el men�.
 *
 *  Par�metros: usuario: c�digo de usuario encriptado
 */
function MenuSimple (usuario) {

	// Usuario
	this.usuario = usuario;

	// Creamos el objeto de men�
	this.divEmergente = new DivEmergente("divMenu", "bordeMenu");
	this.divEmergente.posRel = "d";
	this.divEmergente.zIndex = 15;

}

/*
 * M�todo mostrarMenu()
 *
 *  Muestra el men� que se ha seleccionado
 *
 *  Par�metros: objAncla: objeto de la pantalla en donde se anclar� el men�
 *              codMenu: c�digo del men� seleccionado
 *  Devuelve: nada
 */
MenuSimple.prototype.mostrarMenu = function (objAncla, codMenu) {

	try {
		// Ocultamos lo que estuviese visible
		this.divEmergente.ocultar();

		// Objeto con los datos del men�
		var objMenu = eval("objMenu" + codMenu + ".resultado_consulta");
		if ( objMenu.error != "" ) {
			throw new Error("Error en el men�: " + objMenu.error);
		}

		// Generamos el c�digo que llevar� el DIV
		var contenido = "";
		var objSubmenu = null;
		var clase, onClick, onmouse;
		if ( objMenu.numResultados > 0 ) {
			for ( var k = 0; k < objMenu.registro.length; k++ ) {
				objSubmenu = objMenu.registro[k];
				bPermiso = (objSubmenu.CONSULTA == "S") && (objSubmenu.PANTALLA != "");
				clase = "clMenu" + (bPermiso ? "Enabled" : "Disabled");
				onClick = (bPermiso ? 'onclick="javascript: menu.irAPantalla(\'' + objSubmenu.PANTALLA + '\');"' : "");
				onmouse = (bPermiso ? 'onmouseover="javascript: this.className=\'clMenuEnabledActive\';" onmouseout="javascript: this.className=\'clMenuEnabled\'"' : "");
				// Ahora mismo est� capado para que no muestre los deshabilitados
				var descSubMenu = objSubmenu.DESCRIPCION;
				if ( bPermiso )  contenido += '<div class="' + clase + '" ' + onClick + ' ' + onmouse + (document.all ? ' style="width: 100%;"' : "") + '>' + descSubMenu + '</div>';
			}
			//alert(contenido);
		}
		
		// Nos aseguramos de mostrarlo s�lo si hay contenido
		if ( contenido != "" ) {
			this.divEmergente.objetoAncla = objAncla;
			this.divEmergente.contenido = '<div class="clMenu">' + contenido + '</div>';
			this.divEmergente.mostrar();
		}
	}
	catch (e) {
		alert(e.message);
	}

}

/*
 * M�todo irAPantalla()
 *
 *  Carga en la parte inferior del marco la pantalla seleccionada. Representa la
 *   acci�n que se realiza cada vez que se escoge un submen�.
 *
 *  Par�metros: pantalla: c�digo de la pantalla seleccionada
 *  Devuelve: nada
 */
MenuSimple.prototype.irAPantalla = function (pantalla) {

	var src = "vista.php?pantalla=" + pantalla;
	src += "&usuario=" + this.usuario;

	document.getElementById("framePantalla").src = src;
	this.divEmergente.ocultar();

}