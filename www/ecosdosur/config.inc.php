<?php

/* Fichero de configuraci�n: proyecto ECOS DO SUR */

// Matriz de par�metros de acceso a base de datos
// [OBLIGATORIA] - Conexi�n por defecto
$_db['defecto']['driver'] = 'MySQL';
$_db['defecto']['url'] = 'localhost';
$_db['defecto']['usuario'] = 'ecosdosur';
$_db['defecto']['password'] = 'ecosdosur';
// [OPCIONAL] - Conexi�n alternativa
$_db['alter']['driver'] = 'ODBC';
$_db['alter']['url'] = 'Ecos do Sur';
$_db['alter']['usuario'] = 'ecosdosur';
$_db['alter']['password'] = 'ecosdosur';

// [OBLIGATORIA] Por defecto, se usa esta la conexi�n 'defecto'
$_cnx = 'defecto';
//$_cnx = 'alter';

// [OBLIGATORIA] Clave de encriptaci�n
$_clave = 'ec';

// [OBLIGATORIA] Libreria del sistema
$_libreriaSistema = 'ecosdosur_sistema';

// [OPCIONAL] Consulta de usuario del sistema (para el log)
$_consultaUsuario = 'SELECT USUARIO FROM $_libreriaSistema.usuarios WHERE CUSUARIO=\'$usuarioCodificado\'';

// Rutas del sistema:
// [OBLIGATORIA] Ruta de los ficheros espec�ficos de la aplicaci�n
//$_rutaFicheros = '../ecosdosur/';
$_rutaFicheros = "../$_ap/";
// [OBLIGATORIA] P�gina de inicio
$_pantallaInicio = 'http://www.ecosdosur.org/gallego/principal.htm';
// [OBLIGATORIA] Ruta del fichero de log
//$_rutaLog = '../ecosdosur/logs';
$_rutaLog = $_rutaFicheros . 'logs/';
//$prefijoLog = 'ecosdosur ';

// [OPCIONAL] T�tulo de la aplicaci�n
$_tituloAplicacion = ' :: Intranet  E C O S  D O  S U R ::';

// [OPCIONAL] Direcci�n de correo del administrador
$_emailAdministrador = 'david.alonso@pentared.com';

// Manejo de errores:
// [OPCIONAL] Flag para publicaci�n de errores lanzados por PHP
$_logErroresPHP = false;
// [OPCIONAL] Flag para publicaci�n de errores lanzados por la aplicaci�n
$_logErroresAp = true;

?>