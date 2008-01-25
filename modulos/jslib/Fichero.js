/*
 * Clase Fichero.
 *
 *  Representa un fichero y las operaciones básicas que se pueden realizar con él
 *   en un sistema Windows y en los navegadores Internet Explorer y Mozilla.
 *
 *  Autor: Pentared
 *  Última actualización: 29/11/2006
 */

/*
 * Constructor de la clase
 *
 *  Realiza las operaciones iniciales al crear el objeto
 *
 *  Parámetros: ruta (opcional): ruta del fichero
 */
function Fichero (ruta) {

	// Ruta del fichero
	if ( ruta )  this.ruta = ruta;
	else  this.ruta = "";

}

/*
 * Método setRuta()
 *
 *  Asigna la ruta del fichero. Es igual que hacer  objeto.ruta = ruta;
 *
 *  Parámetros: ruta: cadena con la ruta a asignar
 */
Fichero.prototype.setRuta = function (ruta) {

	this.ruta = ruta;

}

/*
 * Método formateaRuta()
 *
 *  Reemplaza en la ruta las barras normales ('/') por barras invertidas ('\')
 *
 *  Parámetros: no tiene
 */
Fichero.prototype.formateaRuta = function () {

	this.ruta = this.ruta.replace(/\//g, "\\");

}

/*
 * Método getNombre()
 *
 *  Devuelve el nombre del fichero, excluyendo la ruta
 *
 *  Parámetros: no tiene
 *  Devuelve: nombre del fichero, excluyendo la ruta
 */
Fichero.prototype.getNombre = function () {

	this.formateaRuta();

	return this.ruta.substring(this.ruta.lastIndexOf("\\") + 1);

}

/*
 * Método getExtension()
 *
 *  Devuelve la extension del fichero
 *
 *  Parámetros: no tiene
 *  Devuelve: extension del fichero
 */
Fichero.prototype.getExtension = function () {

	this.formateaRuta();

	var indice = this.ruta.lastIndexOf(".", this.ruta.lastIndexOf("\\"));

	return (( indice == -1 ) ? "" : this.ruta.substring(indice + 1));

}

/*
 * Método existe()
 *
 *  Comprueba la existencia del fichero
 *
 *  Parámetros: no tiene
 *  Devuelve: 'true' si existe; 'false' en caso contrario
 *  Lanza excepción si: - ruta vacía
 *                      - error en el navegador
 */
Fichero.prototype.existe = function () {

	// Comprobación previa de ruta vacía
	if ( this.ruta == "" )  throw new Error("Fichero::existe(): ruta vacía");

	var bExisteFichero = false;
	this.formateaRuta();

	try {
		if ( document.all ) {
			// INTERNET EXPLORER
			var objFso = new ActiveXObject("Scripting.FileSystemObject");
			bExisteFichero = objFso.FileExists(this.ruta);
		}
		else {
			// MOZILLA
			netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
			var objFicheroOrigen = Components.classes["@mozilla.org/file/local;1"].createInstance(Components.interfaces.nsILocalFile);
			objFicheroOrigen.initWithPath(this.ruta);
			bExisteFichero = objFicheroOrigen.exists();
		}
	}
	catch (e) {
		throw new Error("Fichero::existe(): " + e.message);
	}

	return bExisteFichero;

}

/*
 * Método existeDir()
 *
 *  Comprueba la existencia de la ruta proporcionada, pero como directorio.
 *
 *  Parámetros: no tiene
 *  Devuelve: 'true' si existe; 'false' en caso contrario
 *  Lanza excepción si: - ruta vacía
 *                      - error en el navegador
 */
Fichero.prototype.existeDir = function () {

	// Comprobación previa de ruta vacía
	if ( this.ruta == "" )  throw new Error("Fichero::existeDir(): ruta vacía");

	var bExisteDirectorio = false;
	this.formateaRuta();

	try {
		if ( document.all ) {
			// INTERNET EXPLORER
			var objFso = new ActiveXObject("Scripting.FileSystemObject");
			bExisteDirectorio = objFso.FolderExists(this.ruta);
		}
		else {
			// MOZILLA
			netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
			var objDirectorioDestino = Components.classes["@mozilla.org/file/local;1"].createInstance(Components.interfaces.nsILocalFile);
			objDirectorioDestino.initWithPath(this.ruta);
			if ( !objDirectorioDestino.exists() )  return false;
			bExisteDirectorio = objDirectorioDestino.isDirectory();
		}
	}
	catch (e) {
		throw new Error("Fichero::existeDir(): " + e.message);
	}

	return bExisteDirectorio;

}

/*
 * Método crearDir()
 *
 *  Crea un directorio en la ruta que se ha especificado.
 *
 *  Parámetros: no tiene
 *  Devuelve: 'true' si se ha creado o 'false' si ya existía como fichero o directorio
 *  Lanza excepción si: - ruta vacía
 *                      - error en el navegador
 */
Fichero.prototype.crearDir = function () {

	// Comprobación previa de ruta vacía
	if ( this.ruta == "" )  throw new Error("Fichero::crearDir(): ruta vacía");

	this.formateaRuta();

	try {
		if ( document.all ) {
			// INTERNET EXPLORER
			var objFso = new ActiveXObject("Scripting.FileSystemObject");
			if ( objFso.FileExists(rutaDirectorio) || objFso.FolderExists(this.ruta) )  return false;
			objFso.CreateFolder(this.ruta);
		}
		else {
			// MOZILLA
			netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
			var objDirectorioDestino = Components.classes["@mozilla.org/file/local;1"].createInstance(Components.interfaces.nsILocalFile);
			objDirectorioDestino.initWithPath(this.ruta);
			if ( objDirectorioDestino.exists() )  return false;
			//if ( objDirectorioDestino.isDirectory() )  return false;
			objDirectorioDestino.create(Components.interfaces.nsIFile.DIRECTORY_TYPE, 0664);
		}
	}
	catch (e) {
		throw new Error("Fichero::crearDir(): " + e.message);
	}

	// Valor devuelto
	return true;

}

/*
 * Método escribir()
 *
 *  Escribe un texto en el fichero que representa este objeto. Sobreescribe el contenido
 *   anterior.
 *
 *  Parámetros: contenido: texto que se quiere escribir en el fichero
 *  Lanza excepción si: - ruta vacía
 *                      - error en el navegador
 */
Fichero.prototype.escribir = function (contenido) {

	// Comprobación previa de ruta vacía
	if ( this.ruta == "" )  throw new Error("Fichero::escribir(): ruta vacía");

	this.formateaRuta();

	try {
		if ( document.all ) {
			// INTERNET EXPLORER
			var objFso = new ActiveXObject("Scripting.FileSystemObject");
			var objFichero = objFso.CreateTextFile(this.ruta, true);
			objFichero.write(contenido);
			objFichero.close();
		}
		else {
			// MOZILLA
			netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
			var objFichero = Components.classes["@mozilla.org/file/local;1"].createInstance(Components.interfaces.nsILocalFile);
			objFichero.initWithPath(this.ruta);
			if ( !objFichero.exists() )  objFichero.create(0x00, 0644);
			var objFlujoSalida = Components.classes["@mozilla.org/network/file-output-stream;1"].createInstance(Components.interfaces.nsIFileOutputStream);
			objFlujoSalida.init(objFichero, 0x20 | 0x04, 00004, null);
			objFlujoSalida.write(contenido, contenido.length);
			objFlujoSalida.flush();
			objFlujoSalida.close();
			/*
			fr = new java.io.FileWriter(rutaFichero);
			fr.write(contenido);
			fr.close();
			*/
		}
	}
	catch (e) {
		throw new Error("Fichero::escribir(): " + e.message);
	}

}

/*
 * Método leer()
 *
 *  Extrae el contenidp del fichero
 *
 *  Parámetros: no tiene
 *  Devuelve: el contenido leído en una única cadena
 *  Lanza excepción si: - ruta vacía
 *                      - el fichero no existe
 *                      - error en el navegador
 */
Fichero.prototype.leer = function () {

	// Comprobación previa de ruta vacía
	if ( this.ruta == "" )  throw new Error("Fichero::leer(): ruta vacía");

	this.formateaRuta();

	// Comprobación de existencia de fichero
	if ( !this.existe() )  throw new Error("Fichero::leer(): no existe el fichero");

	// Valor a devolver
	var contenido = "";

	try {
		if ( document.all ) {
			// INTERNET EXPLORER
			var objFso = new ActiveXObject("Scripting.FileSystemObject");
			var objFichero = objFso.OpenTextFile(this.ruta, 1, false, -2);
			contenido = objFichero.readAll();
			objFichero.close();
		}
		else {
			// MOZILLA
			netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
			var objFichero = Components.classes["@mozilla.org/file/local;1"].createInstance(Components.interfaces.nsILocalFile);
			objFichero.initWithPath(this.ruta);
			var fstream = Components.classes["@mozilla.org/network/file-input-stream;1"].createInstance(Components.interfaces.nsIFileInputStream);
			var sstream = Components.classes["@mozilla.org/scriptableinputstream;1"].createInstance(Components.interfaces.nsIScriptableInputStream);
			fstream.init(objFichero, -1, 0, 0);
			sstream.init(fstream); 

			var data = "";
			var str = sstream.read(4096);
			while (str.length > 0) {
				contenido += str;
				str = sstream.read(4096);
			}

			sstream.close();
			fstream.close();
		}
	}
	catch (e) {
		throw new Error("Fichero::leer(): " + e.message);
	}

	// Valor devuelto
	return contenido;

}

/*
 * Método leerDir()
 *
 *  Recupera los ficheros existentes en el directorio que representa este objeto
 *
 *  Parámetros: no tiene
 *  Devuelve: array con objetos Fichero que representan a cada fichero del directorio
 *  Lanza excepción si: - ruta vacía
 *                      - el directorio no existe
 *                      - error en el navegador
 */
Fichero.prototype.leerDir = function () {

	// Comprobación previa de ruta vacía
	if ( this.ruta == "" )  throw new Error("Fichero::leerDir(): ruta vacía");

	this.formateaRuta();

	// Comprobación de existencia de fichero
	if ( !this.existeDir() )  throw new Error("Fichero::leerDir(): no existe el directorio");

	// Array a devolver
	var arrayFicheros = new Array();

	try {
		if ( document.all ) {
			// INTERNET EXPLORER
			var objFso = new ActiveXObject("Scripting.FileSystemObject");
			var objDirectorio = objFso.GetFolder(this.ruta);
			var enumFicheros = new Enumerator(objDirectorio.files);
			var item = null;
			for ( ; !enumFicheros.atEnd(); enumFicheros.moveNext() ) {
				item = enumFicheros.item();
				if ( item )  arrayFicheros.push(new Fichero("" + item));
			}
		}
		else {
			// MOZILLA
			netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
			var objDirectorio = Components.classes["@mozilla.org/file/local;1"].createInstance(Components.interfaces.nsILocalFile);
			objDirectorio.initWithPath(this.ruta);
			var objListaFicheros = objDirectorio.directoryEntries;
			var item = null;
			while ( objListaFicheros.hasMoreElements() ) {
				item = objListaFicheros.getNext().QueryInterface(Components.interfaces.nsILocalFile);
				if ( !item.isDirectory() )  arrayFicheros.push(new Fichero(item.path));
			}
		}
	}
	catch (e) {
		throw new Error("Fichero::leerDir(): " + e.message);
	}

	// Valor devuelto
	return arrayFicheros;

}