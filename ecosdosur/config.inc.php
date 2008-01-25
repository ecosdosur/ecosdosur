<?php

/* Fichero de configuración: proyecto ECOS DO SUR */

// Matriz de parámetros de acceso a base de datos
// [OBLIGATORIA] - Conexión por defecto
$_db['defecto']['driver'] = 'MySQL';
$_db['defecto']['url'] = 'localhost';
$_db['defecto']['usuario'] = 'ecosdosur';
$_db['defecto']['password'] = 'ecosdosur';
// [OPCIONAL] - Conexión alternativa
$_db['alter']['driver'] = 'ODBC';
$_db['alter']['url'] = 'Ecos do Sur';
$_db['alter']['usuario'] = 'ecosdosur';
$_db['alter']['password'] = 'ecosdosur';

// [OBLIGATORIA] Por defecto, se usa esta la conexión 'defecto'
$_cnx = 'defecto';
//$_cnx = 'alter';

// [OBLIGATORIA] Clave de encriptación
$_clave = 'ec';

// [OBLIGATORIA] Libreria del sistema
$_libreriaSistema = 'ecosdosur_sistema';

// [OPCIONAL] Consulta de usuario del sistema (para el log)
$_consultaUsuario = 'SELECT USUARIO FROM $_libreriaSistema.usuarios WHERE CUSUARIO=\'$usuarioCodificado\'';

// Rutas del sistema:
// [OBLIGATORIA] Ruta de los ficheros específicos de la aplicación
//$_rutaFicheros = '../ecosdosur/';
$_rutaFicheros = "../$_ap/";
// [OBLIGATORIA] Página de inicio
$_pantallaInicio = 'http://www.ecosdosur.org/gallego/principal.htm';
// [OBLIGATORIA] Ruta del fichero de log
//$_rutaLog = '../ecosdosur/logs';
$_rutaLog = $_rutaFicheros . 'logs/';
//$prefijoLog = 'ecosdosur ';

// [OPCIONAL] Título de la aplicación
$_tituloAplicacion = ' :: Intranet  E C O S  D O  S U R ::';

// [OPCIONAL] Dirección de correo del administrador
$_emailAdministrador = 'david.alonso@pentared.com';

// Manejo de errores:
// [OPCIONAL] Flag para publicación de errores lanzados por PHP
$_logErroresPHP = false;
// [OPCIONAL] Flag para publicación de errores lanzados por la aplicación
$_logErroresAp = true;

?>