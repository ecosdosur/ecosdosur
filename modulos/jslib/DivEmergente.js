/*
 * Clase DivEmergente.
 *
 *  Representa DIV emergente y las operaciones que se pueden realizar con él.
 *
 *  Autor: Pentared
 *  Última actualización: 30/11/2006
 */

/*
 * Constructor de la clase
 *
 *  Crea el objeto y permite definir los parámetros, aunque se pueden editar después, siempre
 *   previo a la llamada al método mostrar()
 *
 *  Parámetros: idDiv: id del DIV
 *              idDivExterior: id del DIV exterior.
 *              objetoAncla: objeto que sirve de referencia para colocar este DIV
 *              posRel: posición relativa ('u', 'd', 'l', 'r') (por defecto, 'd')
 *              zIndex: índice de capa (por defecto, 10)
 *              padre: objeto DivEmergente que depende de éste
 *  Lanza excepción si: el parámetro tiene un formato no esperado
 */
function DivEmergente (idDiv, idDivExterior, objetoAncla, posRel, zIndex, padre) {

	// Parámetros configurables desde el constructor
	if ( idDiv )  this.idDiv = idDiv;
	else  this.idDiv = "";
	if ( idDivExterior )  this.idDivExterior = idDivExterior;
	else  this.idDivExterior = "";
	if ( objetoAncla )  this.objetoAncla = objetoAncla;
	else  this.objetoAncla = null;
	if ( posRel )  this.posRel = posRel;
	else  this.posRel = "d";
	if ( zIndex )  this.zIndex = zIndex;
	else  this.zIndex = 10;
	if ( padre )  this.padre = padre;
	else  this.padre = null;

	// Atributo a cubrir más adelante
	this.contenido = "";

	// Offset extra
	this.offsetExtra = 0;

	// NO TOCAR desde fuera
	this.hijo = null;

}

/*
 * Método mostrar()
 *
 *  Muestra el div que se ha configurado previamente. 
 *  Comprueba la validez de los parámetros ('idDiv', 'idDivExterior', 'objetoAncla', 'posRel' y 'zIndex')
 *
 *  Parámetros: no tiene
 *  Lanza excepción si: alguno de los parámetros no es correcto
 */
DivEmergente.prototype.mostrar = function () {

	// Control de id principal válido
	if ( this.idDiv == "" )  throw new Error("DivEmergente::mostrar(): 'idDiv' no está cubierto");
	var objDiv = document.getElementById(this.idDiv);
	if ( !objDiv )  throw new Error("DivEmergente::mostrar(): no existe el DIV (" + this.idDiv + ")");

	// Control de id principal válido
	if ( this.idDivExterior == "" )  throw new Error("DivEmergente::mostrar(): 'idDivExterior' no está cubierto");
	var objDivExterior = document.getElementById(this.idDivExterior);
	if ( !objDivExterior )  throw new Error("DivEmergente::mostrar(): no existe el DIV (" + this.idDivExterior + ")");

	// Pre-estilo del DIV
	objDiv.style.position = "absolute";
	objDiv.innerHTML = "";
	objDiv.style.display = "";

	// Control de objetoAncla
	if ( this.objetoAncla == null )  throw new Error("DivEmergente::mostrar(): no existe 'objetoAncla'");

	// Control de posición relativa
	if ( this.posRel == "" )  this.posRel = "d";
	this.posRel = this.posRel.toLowerCase();
	if ( (this.posRel.length > 1) || ("udlr".indexOf(this.posRel) == -1) ) {
		throw new Error("DivEmergente::mostrar(): 'posRel' no válido (" + this.posRel + ")");
	}

	// Control de offset extra
	if ( isNaN(this.offsetExtra) )  throw new Error("DivEmergente::mostrar(): 'offsetExtra' no válido (" + this.offsetExtra + ")");

	// Control de contenido
	if ( this.contenido == "" )  throw new Error("DivEmergente::mostrar(): 'contenido' está vacío");

	// Posicionamiento
	// - Offsets absolutos del objeto ancla
	var objetoAux = this.objetoAncla;
	var offsetTop = 0, offsetLeft = 0;
	do {
		offsetTop += objetoAux.offsetTop;
		offsetLeft += objetoAux.offsetLeft;
	} while ( (objetoAux = objetoAux.offsetParent) != null );
	// - Posición del nuevo DIV
	var ancho = this.objetoAncla.offsetWidth;
	if ( ancho == "" )  throw new Error("DivEmergente::mostrar(): no se puede obtener el ancho");
	ancho = parseInt(ancho);
	var alto = this.objetoAncla.offsetHeight;
	if ( alto == "" )  throw new Error("DivEmergente::mostrar(): no se puede obtener el alto");
	alto = parseInt(alto);
	var anchoDiv = objDiv.offsetWidth;
	if ( anchoDiv == "" )  throw new Error("DivEmergente::mostrar(): no se puede obtener el ancho del nuevo DIV");
	anchoDiv = parseInt(anchoDiv);
	var top = 0, left = 0;
	switch ( this.posRel ) {
		case 'u': top = offsetTop; left = offsetLeft; break;
		case 'd': top = offsetTop + alto + this.offsetExtra; left = offsetLeft; break;
		case 'l': top = offsetTop; left = offsetLeft - anchoDiv - this.offsetExtra; break;
		case 'r': top = offsetTop; left = offsetLeft + ancho + this.offsetExtra; break;
	}

	// Estilo del DIV principal
	objDiv.style.zIndex = this.zIndex;
	objDiv.style.top = top;
	objDiv.style.left = left;

	// Lo hacemos, finalmente, visible con su nuevo contenido
	objDiv.innerHTML = this.contenido;

	// Estilo del div exterior
	var borde = 2;
	objDivExterior.style.position = "absolute";
	objDivExterior.innerHTML = "";
	objDivExterior.style.display = "";
	objDivExterior.style.zIndex = this.zIndex - 1;
	var topExt = 0, leftExt = 0, widthExt = 0, heightExt = 0;
	switch ( this.posRel ) {
		case 'u': {
			topExt = top - borde;
			leftExt = left - borde;
			widthExt = objDiv.offsetWidth + 2*borde;
			heightExt = objDiv.offsetHeight + 2*borde;
			break;
		}
		case 'd': {
			topExt = top;
			leftExt = left - borde;
			widthExt = objDiv.offsetWidth + 2*borde;
			heightExt = objDiv.offsetHeight + borde;
			break;
		}
		case 'l': {
			topExt = top - borde;
			leftExt = left - borde;
			widthExt = objDiv.offsetWidth + borde;
			heightExt = objDiv.offsetHeight + 2*borde;
			break;
		}
		case 'r': {
			topExt = top - borde;
			leftExt = left;
			widthExt = objDiv.offsetWidth + borde;
			heightExt = objDiv.offsetHeight + 2*borde;
			break;
		}
	}
	objDivExterior.style.top = topExt;
	objDivExterior.style.left = leftExt;
	objDivExterior.style.width = widthExt;
	objDivExterior.style.height = heightExt;

	// Indicamos al padre que tiene un hijo
	if ( this.padre != null )  this.padre.hijo = this;

	// El DIV exterior y si tiene hijo indicará cuándo ocultar el DIV
	var objThis = this;
	objDivExterior.onmouseover = function () {
		// Si no tiene hijos, se oculta y se desvincula del padre (si lo tiene)
		if ( objThis.hijo == null ) {
			objDiv.style.display = objDivExterior.style.display = "none";
			if ( objThis.padre != null )  objThis.padre.hijo = null;
		}
	}

}

/*
 * Método ocultar()
 *
 *  Oculta el div que se ha configurado previamente. 
 *  Comprueba la validez de los parámetros ('idDiv', 'idDivExterior', 'objetoAncla', 'posRel' y 'zIndex')
 *
 *  Parámetros: no tiene
 *  Lanza excepción si: alguno de los parámetros no es correcto
 */
DivEmergente.prototype.ocultar = function () {

	// Ocultamos nuestro hijo y sucesivos
	if ( this.hijo != null )  this.hijo.ocultar();
	
	// No ocultamos a nosotros mismos
	var objDiv = document.getElementById(this.idDiv);
	var objDivExterior = document.getElementById(this.idDivExterior);
	if ( !objDiv )  throw new Error("DivEmergente::ocultar(): no existe 'idDiv' (" + this.idDiv + ")");
	if ( !objDivExterior )  throw new Error("DivEmergente::ocultar(): no existe 'idDivExterior' (" + this.idDivExterior + ")");
	objDiv.style.display = objDivExterior.style.display = "none";

}