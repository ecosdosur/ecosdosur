<?php

/**************************************************\
 **** APLICACI�N DE MANTENIMIENTO DE INTRANETS **** 
 **************************************************
 ********* M�dulo de pantalla de error ************
\**************************************************/

// Fichero de inclusi�n
require_once 'includes.inc.php';

?>

<html>
<head>
	<title>Error en la aplicaci�n</title>
	<link rel="stylesheet" type="text/css" href="<?php echo $_rutaFicheros; ?>estilos.css" />
</head>
<body>
	<div class="clCabecera" style="padding: 5px;">Error en la aplicaci�n</div>
	<p />
	<span style="font-size: 14px;"><b>Se ha producido el siguiente error en la aplicaci�n:</b></span>
	<p class="clMsgEncuadrado">
		<i><?php echo $_GET['msgerror']; ?></i>
	</p>
	<p />
	<span style="font-size: 13px;">P�ngase en contacto con el <a href="mailto: <?php echo $_emailAdministrador; ?>?subject=Error en la aplicaci�n <?php echo $_tituloAplicacion; ?>">administrador</a> para subsanarlo lo antes posible.</span>
	<p />
	<input type="button" class="boton" id="btnCerrar" style="width: 100px;" value="Cerrar" onclick="javascript: window.close();" onmouseover="javascript: if ( !this.disabled ) { this.className='botonOver'; };" onmouseout="javascript: this.className='boton';" />
</body>
</html>