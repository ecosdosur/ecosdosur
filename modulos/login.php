<?php

/**************************************************\
 **** APLICACIÓN DE MANTENIMIENTO DE INTRANETS **** 
 **************************************************
 ******* Módulo de acceso a la aplicación *********
\**************************************************/

// Fichero de inclusión
require_once 'includes.inc.php';

// Forzamos el tipo adecuado
header("Content-Type: text/html;charset=ISO-8859-1");

?>
<html>
<head>
	<title><?php echo $_tituloAplicacion; ?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo $_rutaFicheros; ?>estilos.css" />
	<script language="JavaScript" type="text/javascript" src="jslib/String.js"></script>
	<script language="JavaScript" type="text/javascript" src="jslib/Ventana.js"></script>
	<script language="JavaScript" type="text/javascript" src="jslib/AJAXRequest.js"></script>
	<script language="JavaScript" type="text/javascript" src="jslib/Login.js"></script>
	<script language="JavaScript" type="text/javascript">
		var login = new Login('<?php echo $_libreriaSistema; ?>', '<?php echo $_clave; ?>');
	</script>
</head>
<body onload="javascript: login.inicio();">
	<table border="0" style="width: 30%; height: 100%;" align="center">
		<tr>
			<td style="text-align: center;">
				<div id="marcoLogin">
					<div id="cabeceraLogin" class="clCabeceraLogin">
						<span>ACCESO AL SISTEMA</span>
					</div>
					<div id="cuerpoLogin" class="clCuerpoLogin">
						<table class="clTablaCuerpoLogin">
							<tr>
								<td align="right" width="30%">Usuario:</td>
								<td>
									<input type="text" class="inputTexto" size="22" id="usuario"/>
								</td>
							</tr>
							<tr>
								<td align="right">Contraseña:</td>
								<td>
									<input type="password" class="inputTexto" size="22" id="passwd" onKeyDown="javascript: login.accionKeyDown(this, event);"/>
								</td>
							</tr>
							<tr>
								<td></td>
								<td>
									<input type="button" id="btnLogin" class="boton" value="Entrar" style="width: 100px;" onclick="javascript: login.entrar();" onmouseover="javascript: if ( !this.disabled ) { this.className='botonOver'; };" onmouseout="javascript: this.className='boton';"/>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</td>
		</tr>
	</table>
</body>
</html>