<?php

/**************************************************\
 **** APLICACIÓN DE MANTENIMIENTO DE INTRANETS **** 
 **************************************************
 ******** Módulo de edición de pantallas **********
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
	header('location: error.php?msgerror=' . $ex->getMessage());
}

?>
<html>
<head>
	<title>Edición de registro</title>
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
	<script language="JavaScript" type="text/javascript" src="jslib/Edicion.js"></script>
	<?php
		$pantalla->pintaScripts();
	?>
	<script language="JavaScript" type="text/javascript">
		var objJSON = eval('(<?php echo $pantalla->toJSON(); ?>)');
		var edicion = new Edicion(objJSON.pantalla);
		<?php
			try {
				$pantalla->pintaObjetosCamposRelacionados();
			}
			catch (Exception $ex) {
				echo "alert('".  str_replace("'", "\'", $ex->getMessage()) . "');";
			}
		?>
		var libreriaSistema = '<?php echo $_libreriaSistema; ?>';
		var clave = '<?php echo $_clave; ?>';
	</script>
</head>
<body onload="javascript: edicion.inicio();">
	<div id="popupcalendar"></div>
	<div id="extCabecera">
		<div id="inCabecera" class="clCabecera">
			<span>Edición de elemento: <?php echo $pantalla->getDescripcion(); ?></span>
		</div>
	</div>
	<div id="panel">
		<div id="campos">
			<?php
				try {
					$pantalla->pintaPlantillaCampos();
				}
				catch (Exception $ex) {
					echo $ex->getMessage();
				}
			?>
		</div>
		<div id="botones" style="display: none;">
			<table border="0" width="100%" cellpadding="15">
				<tr>
					<td align="left">
						<input type="button" class="boton" id="btnCancelar" style="width: 100px;" value="Cancelar" title="Cierra esta ventana" onclick="javascript: window.close();" onmouseover="javascript: if ( !this.disabled ) { this.className='botonOver'; };" onmouseout="javascript: this.className='boton';" />
						<input type="button" class="boton" id="btnLimpiar" style="width: 100px; display: none;" value="Limpiar" title="Borra todos los campos" onclick="javascript: edicion.limpiar();" onmouseover="javascript: if ( !this.disabled ) { this.className='botonOver'; };" onmouseout="javascript: this.className='boton';" />
					</td>
					<td align="right">
						<input type="button" class="boton" id="btnNuevo" style="width: 100px; display: none;" value="Nuevo" title="Crea un nuevo registro" onclick="javascript: edicion.nuevo();" onmouseover="javascript: if ( !this.disabled ) { this.className='botonOver'; };" onmouseout="javascript: this.className='boton';" />
						<input type="button" class="boton" id="btnModificar" style="width: 100px; display: none;" value="Modificar" title="Actualiza este registro" onclick="javascript: edicion.modificar();" onmouseover="javascript: if ( !this.disabled ) { this.className='botonOver'; };" onmouseout="javascript: this.className='boton';" />
					</td>
				</tr>
			</table>
		</div>
		<div id="extMsgAccion" style="display: none;">
			<div id="inMsgAccion" class="clMsgAccion">
				<span id="textoAccion"></span>
			</div>
		</div>
	</div>
</body>
</html>