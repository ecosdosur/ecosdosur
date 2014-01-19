<?php

/**************************************************\
 **** APLICACIÓN DE MANTENIMIENTO DE INTRANETS **** 
 **************************************************
 ********* Módulo de vista de pantallas ***********
\**************************************************/

// Fichero de inclusión
require_once 'includes.inc.php';

// Objeto de gestión de la pantalla
$pantalla = null;

// Ejecución del módulo
try {
	$pantalla = new Pantalla($_libreriaSistema, $_GET);
	header("Content-Type: text/html;charset=ISO-8859-1");
}
catch (Exception $ex) {
	// Caso de error
	//echo $ex->getMessage();
	header('location: error.php?msgerror=' . $ex->getMessage());
}

?>
<html>
<head>
	<title>Lista de <?php echo $pantalla->getDescripcion(); ?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo $_rutaFicheros; ?>estilos.css" />
	<link rel="stylesheet" type="text/css" media="all" href="jslib/calendar-brown.css" />
	<script language="JavaScript" type="text/javascript" src="jslib/calendar.js"></script>
	<script language="JavaScript" type="text/javascript" src="jslib/calendar-es.js"></script>
	<script language="JavaScript" type="text/javascript" src="jslib/calendar-post.js"></script>
	<script language="JavaScript" type="text/javascript" src="jslib/String.js"></script>
	<script language="JavaScript" type="text/javascript" src="jslib/Fecha.js"></script>
	<script language="JavaScript" type="text/javascript" src="jslib/DivEmergente.js"></script>
	<script language="JavaScript" type="text/javascript" src="jslib/Ventana.js"></script>
	<script language="JavaScript" type="text/javascript" src="jslib/AJAXRequest.js"></script>
	<script language="JavaScript" type="text/javascript" src="jslib/Vista.js"></script>
	<?php
		$pantalla->pintaScripts();
	?>
	<script language="JavaScript" type="text/javascript">
		var objJSON = eval('(<?php echo $pantalla->toJSON(); ?>)');
		var vista = new Vista(objJSON.pantalla);
		var libreriaSistema = '<?php echo $_libreriaSistema; ?>';
	</script>
</head>
<body onload="javascript: vista.inicio();">
	<div id="bordeMenu" style="display: none;"></div>
	<div id="menu" style="display: none;"></div>
	<div id="extCabecera">
		<div id="inCabecera" class="clCabecera">
			<span><?php echo $pantalla->getDescripcion(); ?></span>
			<div id="divBotonArriba">
				<input type="button" id="btnArriba" value="" class="boton" disabled="disabled" style="width: 100px; display: none;" onmouseover="javascript: if ( !this.disabled ) { this.className='botonOver'; };" onmouseout="javascript: this.className='boton';" onclick="javascript: vista.accionBotonArriba();" />
			</div>
		</div>
	</div>
	<div id="panel">
		<div id="extDescPadre" style="display: none;">
			<div id="inDescPadre">
				<span id="txtDescPadre"></span>
			</div>
		</div>
		<div id="extFiltro" style="display: none;">
			<div id="inFiltro">
				<table border="0">
					<tr>
						<td valign="top" width="100px">
							<span id="textoFiltro">Filtra por...</span>
						</td>
						<td>
							<div id="filtros"></div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div id="extResultado" style="display: none;">
			<div id="inResultado">
				<span id="textoResultado"></span>
			</div>
		</div>
		<div id="extNavegador" style="display: none;">
			<div id="inNavegador" class="clNavegador">
				<span id="textoNavegador"></span>
				<input type="button" id="btnInicio" value="<<" class="boton" disabled="disabled" style="width: 50px;" title="Ir a la primera página" onmouseover="javascript: if ( !this.disabled ) { this.className='botonOver'; };" onmouseout="javascript: this.className='boton';" onclick="javascript: vista.ir(this, 'I');" />
				<input type="button" id="btnAnterior" value="<" class="boton" disabled="disabled" style="width: 50px;" title="Ir a la página anterior" onmouseover="javascript: if ( !this.disabled ) { this.className='botonOver'; };"  onmouseout="javascript: this.className='boton';" onclick="javascript: vista.ir(this, 'A');" />
				<input type="button" id="btnSiguiente" value=">" class="boton" disabled="disabled" style="width: 50px;" title="Ir a la página siguiente" onmouseover="javascript: if ( !this.disabled ) { this.className='botonOver'; };"  onmouseout="javascript: this.className='boton';"  onclick="javascript: vista.ir(this, 'S');"/>
				<input type="button" id="btnFinal" value=">>" class="boton" disabled="disabled" style="width: 50px;" title="Ir a la última página" onmouseover="javascript: if ( !this.disabled ) { this.className='botonOver'; };"  onmouseout="javascript: this.className='boton';" onclick="javascript: vista.ir(this, 'F');" />
			</div>
		</div>
		<div id="extMsgAccion" style="display: none;">
			<div id="inMsgAccion" class="clMsgAccion">
				<span id="textoAccion"></span>
			</div>
		</div>
	</div>
</body>
</html>