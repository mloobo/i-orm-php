<?php

/**
 * Aqu� disponemos de las variables de configuraci�n del ORM.
 */


/**
 * URL del HOST de la base de datos de la aplicaci�n
 * @var string
 */
define("ORM_HOST", "");

/**
 * Nombre de usuario para conectarse a la base de datos.
 * @var string
 */
define("ORM_USUARIO", "");

/**
 * Contrase�a de usurario para conectarse a la base de datos.
 * @var string
 */
define("ORM_PASS", "");

/**
 * Nombre de la base de datos que vamos a gestionar.
 * @var string
 */
define("ORM_BD", "");

/**
 * El tipo de base de datos que vamos a utilizar. De momento s�lo hay definido
 * el driver para el sistema MySQL.
 * @var string
 */
define("ORM_DRIVER_BD", "MySQL");

/**
 * Define el n�mero de versi�n de MySQL, para el caso de que se use el driver MySQL.
 * Si no se usa el driver de MySQL, se puede dejar vacio
 * @var string
 */
define("ORM_DRIVER_BD_MYSQL_VERSION", "4.018");

/**
 * Variable para obtener informaci�n de DEBUG mientras desarrollamos la 
 * aplicaci�n.
 * @var bool
 */
define("ORM_DEBUG", true);

?>