<?php

/**
 * Aquí disponemos de las variables de configuración del ORM.
 */


/**
 * URL del HOST de la base de datos de la aplicación
 * @var string
 */
define("ORM_HOST", "");

/**
 * Nombre de usuario para conectarse a la base de datos.
 * @var string
 */
define("ORM_USUARIO", "");

/**
 * Contraseña de usurario para conectarse a la base de datos.
 * @var string
 */
define("ORM_PASS", "");

/**
 * Nombre de la base de datos que vamos a gestionar.
 * @var string
 */
define("ORM_BD", "");

/**
 * El tipo de base de datos que vamos a utilizar. De momento sólo hay definido
 * el driver para el sistema MySQL.
 * @var string
 */
define("ORM_DRIVER_BD", "MySQL");

/**
 * Define el número de versión de MySQL, para el caso de que se use el driver MySQL.
 * Si no se usa el driver de MySQL, se puede dejar vacio
 * @var string
 */
define("ORM_DRIVER_BD_MYSQL_VERSION", "4.018");

/**
 * Variable para obtener información de DEBUG mientras desarrollamos la 
 * aplicación.
 * @var bool
 */
define("ORM_DEBUG", true);

?>