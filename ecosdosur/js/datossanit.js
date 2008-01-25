/*
 *  Funcionalidades específicas de la pantalla de datos sanitarios de usuarios del Ecos do Sur
 *
 *  Autor: Pentared
 *  Última actualización: 22/01/2007
 */

// Control de código: ha de estar incluido el fichero 'Edicion.js'
if ( typeof Edicion != "undefined" ) {


/*
 * Método finRecuperar()
 *
 *  Método que se llama tras acabar con las tareas de fin de recuperación. Sobrepone el método original.
 *
 *  Parámetros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.finRecuperar = function () {

	// Llamada para recuperar datos extra
	this.recuperarDatosExtraUsuario();

}

/*
 * Método finVolverLista()
 *
 *  Realiza las acciones posteriores a la vuelta de la lista. Sobrepone el método original.
 *   ficheros específicos
 *
 *  Parámetros: arrayCampos: lista de campos en los que se han volcado los datos devueltos por la lista
 *  Devuelve: nada
 */
Edicion.prototype.finVolverLista = function (arrayCampos) {

	// Llamada para recuperar datos extra
	this.recuperarDatosExtraUsuario();

}

/*
 * Método recuperarDatosExtraUsuario()
 *
 *  Realiza la llamada al servidor de consulta para obtener los datos informativos
 *   adicionales del usuario que se ha recuperado.
 *
 *  Parámetros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.recuperarDatosExtraUsuario = function () {

	try {
		// Historial
		var historial = document.getElementById("HISTORIAL").value;
		if ( historial == "" ) {
			// Vuelta al flujo normal de la aplicación
			this.accionFinRecuperar();
			return;
		}
		// Consulta para recuperar los datos
		var consulta = "SELECT usuarios.SEXO, usuarios.LOCALIDAD, municipios.DESCRIPCION AS DESCMUNICIPIO, provincias.DESCRIPCION AS DESCPROVINCIA, nacionalidades.DESCRIPCION AS DESCNACIONALIDAD, areasgeo.DESCRIPCION AS DESCAREAGEO FROM ecosdosur.usuarios AS usuarios LEFT JOIN ecosdosur.municipios AS municipios ON municipios.CODMUNICIPIO=usuarios.CODMUNICIPIO LEFT JOIN ecosdosur.provincias AS provincias ON provincias.CODPROV=usuarios.CODPROV LEFT JOIN ecosdosur.nacionalidades AS nacionalidades ON nacionalidades.CODNACIONAL=usuarios.CODNACIONAL LEFT JOIN ecosdosur.areasgeo AS areasgeo ON areasgeo.CODAREA=usuarios.CODAREA WHERE usuarios.HISTORIAL='" + historial + "'";
		// Objeto para llamada asíncrona
		var objAjax = new AJAXRequest();
		objAjax.setUrl("../modulos/servidor.php");
		objAjax.addParametro("tipoServidor", "Consulta");
		objAjax.addParametro("numAcciones", "1");
		objAjax.addParametro("sql", consulta);
		//alert(consulta);
		// Información para log
		//objAjax.addParametro("usuario", this.pantalla.usuario);
		//objAjax.addParametro("libtabla", this.pantalla.libreriaTabla);
		var objThis = this;
		objAjax.setFuncionDeVuelta(function () { objThis.vueltaRecuperarDatosExtraUsuario(objAjax); });
		objAjax.enviar();
		// Mensaje de búsqueda
		this.mostrarAccion("RECUPERANDO REGISTRO...");
	}
	catch (e) {
		alert(e.message);
		// Vuelta al flujo normal de la aplicación
		this.accionFinRecuperar();
	}

}

/*
 * Método vueltaRecuperarDatosExtraUsuario()
 *
 *  Comprueba que los datos obtenidos en la llamada anterior son correctos, los
 *   cubre adecuadamente y retorna al flujo normal de la aplicación
 *
 *  Parámetros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.vueltaRecuperarDatosExtraUsuario = function (objAjax) {

	try {
		if ( !objAjax.finCarga() )  return;
		if ( objAjax.hayErrorHTTP() )  throw new Error(objAjax.getErrorHTTP());

		//alert(objAjax.objHttpRequest.responseText);
		var objResultado = objAjax.getRespuestaJSON().resultado_consulta;
		//alert(objetoRespuesta.contenido);
		
		// Control de error:
		if ( objResultado.error != "" )  throw new Error(objResultado.error);

		// Control de resultado existente
		if ( objResultado.numResultados == 0 )  throw new Error("El registro al que pretende acceder no existe");

		// Datos extra recuperados
		var registro = objResultado.registro[0];
		// - Residencia
		var residencia = registro.DESCMUNICIPIO;
		if ( (registro.DESCPROVINCIA != "") && (registro.DESCPROVINCIA != registro.DESCMUNICIPIO) ) {
			residencia += " (" + registro.DESCPROVINCIA + ")";
		}
		if ( (registro.LOCALIDAD != "") && (registro.LOCALIDAD != registro.DESCMUNICIPIO) ) {
			if ( residencia != "" )  residencia = " - " + residencia;
			residencia = registro.LOCALIDAD + residencia;
		}
		document.getElementById("_RESIDENCIA").value = residencia;
		// - Sexo
		document.getElementById("_SEXO").value = (registro.SEXO == "H" ? "Hombre" : "Mujer");
		// - Nacionalidad
		document.getElementById("_NACIONALIDAD").value = registro.DESCNACIONALIDAD;
		// - Área geográfica
		document.getElementById("_AREAGEO").value = registro.DESCAREAGEO;
	}
	catch (e) {
		alert(e.message);
	}

	// Vuelta al flujo normal de la aplicación en cualquier caso
	this.accionFinRecuperar();

}


/*
 * Método finLimpiar()
 *
 *  Método que sobrepone al existente en la clase Edicion. Borra los elementos adicionales
 *   de esta pantalla
 *
 *  Parámetros: no tiene
 *  Devuelve: nada
 */
Edicion.prototype.finLimpiar = function () {

	// No se borra nada si venimos de la pantalla padre
	if ( this.pantalla.camposFijos != "" )  return;

	var arrayCamposExtra = ["_RESIDENCIA", "_SEXO", "_NACIONALIDAD", "_AREAGEO"];
	for ( var k in arrayCamposExtra ) {
		document.getElementById(arrayCamposExtra[k]).value = "";
	}

}

/*
 * Método finBorrarRelacionado()
 *
 *  Método que sobrepone al existente en la clase Edicion. Borra los elementos adicionales
 *   de esta pantalla
 *
 *  Parámetros: campo: nombre del campo relacionado
 *              descCampo: nombre del campo descripción
 *  Devuelve: nada
 */
Edicion.prototype.finBorrarRelacionado = function (campo, descCampo) {

	// Se borran el resto de los campos
	this.finLimpiar();

}


}