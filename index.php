<?php
require_once 'core/includes.php';
require_once 'test.php';
/**
 * Página de ejemplo del funcionamiento del ORM.
 * 
 * En este ejemplo tenemos un modelo Usuario que se distingue por el tipo
 * de usuario que puede ser (administrador, normal, etc.). 
 * 
 * 
 */
?>
<html>
<head>
	<title>Test: Pruebas de la clase /test.php</title>
	<style>
	.iormphp_excepcion{
		background-color: #FF0000;
		color: #FFFFFF;
	}
	</style>
</head>
<body>
<pre>
<?php 
$t=new Test();

//$t->test_BaseDatos();

//$t->test_Find();

//$t->test_FindBy();

//$t->test_FindCampoBy();

//$t->test_insertar();

$t->test_actualizar();

?>
</pre>
</body>
</html>

