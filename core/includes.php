<?php

/**
 * Archivo para la inclusión de archivos necesarios.
 */

/**
 * Se define la ruta absoluta al sistema
 * @var string
 */
define('ROOT', dirname(__FILE__));

require_once ROOT.'/../config.php';

if(ORM_HOST=="" || ORM_USUARIO=="" || ORM_PASS=="" || ORM_BD==""){
	echo "Falta algún dato de configuración.";
	exit();
}

if(ORM_DRIVER_BD==""){
	echo "No se ha definido un driver para el sistema.";
	exit();
}

$driver=ROOT.'/drivers/'.ORM_DRIVER_BD.'.php';
if(!file_exists($driver)){
	echo "El driver '".ORM_DRIVER_BD."' no está definido en el directorio de drivers o no existe el archivo '".$driver."'";
	exit();
}

require_once ROOT.'/BaseDatos.php';
require_once $driver;
require_once ROOT.'/Modelo.php';

require_once ROOT.'/excepciones/Excepcion.php';

//cargamos los modelos que haya definidos
$dirModelos=ROOT.'/../modelos/';
$d = dir($dirModelos);
while (false !== ($entry = $d->read())) {
	if($entry != '.' && $entry != '..' &&  strcasecmp(substr($entry, -3, 3), "php")==0){
		require_once $dirModelos.$entry;
	}
}
$d->close();
?>