<?php

/**************************************************\
 **** APLICACIÓN DE MANTENIMIENTO DE INTRANETS **** 
 **************************************************
 ******* Módulo del marco de la aplicación ********
\**************************************************/

// Fichero de inclusión
require_once 'includes.inc.php';

// Objeto de gestión del menú
$menu = null;

// Ejecución del módulo
try {
	$menu = new MenuSimple($_libreriaSistema, $_GET);
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
	<title><?php echo $_tituloAplicacion; ?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo $_rutaFicheros; ?>estilos.css" />
	<script language="JavaScript" type="text/javascript" src="jslib/DivEmergente.js"></script>
	<script language="JavaScript" type="text/javascript" src="jslib/AJAXRequest.js"></script>
	<script language="JavaScript" type="text/javascript" src="jslib/MenuSimple.js"></script>
	<script language="JavaScript" type="text/javascript">
	<?php
		$menu->pintaObjetosSubmenus();
	?>
		var menu = new MenuSimple('<?php echo $menu->getUsuario(); ?>');
	</script>
</head>
<body>
	<div id="divMenu" style="display: none;"></div>
	<div id="bordeMenu" style="display: none;"></div>
	<div id="extBarraMenus">
		<div id="inBarraMenus">
			<?php
				$menu->pintaBarraMenus();
			?>
		</div>
	</div>
	<iframe id="framePantalla" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="95%" src="<?php echo $_pantallaInicio; ?>"></iframe>
</body>
</html>