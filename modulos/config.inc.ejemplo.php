<?php

/* Fichero de configuraci�n: proyecto ECOS DO SUR */

// Matriz de par�metros de acceso a base de datos
// [OBLIGATORIA] - Conexi�n por defecto
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
// [OPCIONAL] Conexi�n por ODBC a MySQL
$_db['odbc1']['driver'] = 'ODBC';
$_db['odbc1']['url'] = 'MySQL Local';
$_db['odbc1']['usuario'] = 'root';
$_db['odbc1']['password'] = 'root';
// [OPCIONAL] Conexi�n por ODBC a Access
$_db['odbc2']['driver'] = 'ODBC';
$_db['odbc2']['url'] = 'Access Local';
//$_db['odbc2']['url'] = 'DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=D:\\Inetpub\\data\\access\\pruebas.mdb';
$_db['odbc2']['usuario'] = 'root';
$_db['odbc2']['password'] = 'root';

// [OBLIGATORIA] Por defecto, se usa esta la conexi�n 'defecto'
$_cnx = 'defecto';

// [OBLIGATORIA] Clave de encriptaci�n
$_clave = 'ec';

// [OBLIGATORIA] Libreria del sistema. Si es un ODBC contra Access, dejar vac�a
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