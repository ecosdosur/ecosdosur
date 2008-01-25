/*
 * Clase MenuSimple.
 *
 *  Representa un menú simple con un solo nivel de submenús.
 *
 *  IMPORTANTE: debe tener incluido antes los siguientes ficheros: 'DivEmergente.js'
 *
 *  Autor: Pentared
 *  Última actualización: 07/02/2007
 */


/*
 * Constructor de la clase
 *
 *  Realiza las operaciones básicas para preparar el menú.
 *
 *  Parámetros: usuario: código de usuario encriptado
 */
function MenuSimple (usuario) {

	// Usuario
	this.usuario = usuario;

	// Creamos el objeto de menú
	this.divEmergente = new DivEmergente("divMenu", "bordeMenu");
	this.divEmergente.posRel = "d";
	this.divEmergente.zIndex = 15;

}

/*
 * Método mostrarMenu()
 *
 *  Muestra el menú que se ha seleccionado
 *
 *  Parámetros: objAncla: objeto de la pantalla en donde se anclará el menú
 *              codMenu: código del menú seleccionado
 *  Devuelve: nada
 */
MenuSimple.prototype.mostrarMenu = function (objAncla, codMenu) {

	try {
		// Ocultamos lo que estuviese visible
		this.divEmergente.ocultar();

		// Objeto con los datos del menú
		var objMenu = eval("objMenu" + codMenu + ".resultado_consulta");
		if ( objMenu.error != "" ) {
			throw new Error("Error en el menú: " + objMenu.error);
		}

		// Generamos el código que llevará el DIV
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
				// Ahora mismo está capado para que no muestre los deshabilitados
				var descSubMenu = objSubmenu.DESCRIPCION;
				if ( bPermiso )  contenido += '<div class="' + clase + '" ' + onClick + ' ' + onmouse + (document.all ? ' style="width: 100%;"' : "") + '>' + descSubMenu + '</div>';
			}
			//alert(contenido);
		}
		
		// Nos aseguramos de mostrarlo sólo si hay contenido
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
 * Método irAPantalla()
 *
 *  Carga en la parte inferior del marco la pantalla seleccionada. Representa la
 *   acción que se realiza cada vez que se escoge un submenú.
 *
 *  Parámetros: pantalla: código de la pantalla seleccionada
 *  Devuelve: nada
 */
MenuSimple.prototype.irAPantalla = function (pantalla) {

	var src = "vista.php?pantalla=" + pantalla;
	src += "&usuario=" + this.usuario;

	document.getElementById("framePantalla").src = src;
	this.divEmergente.ocultar();

}