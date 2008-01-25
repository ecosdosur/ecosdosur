<?php

/* Fichero de configuración: proyecto ECOS DO SUR */

// Matriz de parámetros de acceso a base de datos
// [OBLIGATORIA] - Conexión por defecto
$_db['defecto']['driver'] = 'MySQL';
$_db['defecto']['url'] = 'localhost';
$_db['defecto']['puerto'] = '3306';      // No es obligatorio (en caso de 3306)
$_db['defecto']['usuario'] = 'root';
$_db['defecto']['password'] = 'root';
// - Ejemplo de otras conexiones
//$_db['cnx1']['driver'] = 'ODBC';
//$_db['cnx1']['url'] = 'localhost';
//$_db['cnx1']['usuario'] = 'usuario';
//$_db['cnx1']['password'] = 'pass';
// [OPCIONAL] Conexión por ODBC a MySQL
$_db['odbc1']['driver'] = 'ODBC';
$_db['odbc1']['url'] = 'MySQL Local';
$_db['odbc1']['usuario'] = 'root';
$_db['odbc1']['password'] = 'root';
// [OPCIONAL] Conexión por ODBC a Access
$_db['odbc2']['driver'] = 'ODBC';
$_db['odbc2']['url'] = 'Access Local';
//$_db['odbc2']['url'] = 'DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=D:\\Inetpub\\data\\access\\pruebas.mdb';
$_db['odbc2']['usuario'] = 'root';
$_db['odbc2']['password'] = 'root';

// [OBLIGATORIA] Por defecto, se usa esta la conexión 'defecto'
$_cnx = 'defecto';

// [OBLIGATORIA] Clave de encriptación
$_clave = 'ec';

// [OBLIGATORIA] Libreria del sistema. Si es un ODBC contra Access, dejar vacía
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