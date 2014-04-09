<?php
/**
 * Clase de test para probar las funcionalidades de la clase Modelo
 * 
 * @name Test
 * @author lobo (mloobo@gmail.com)
 *
 */
class Test{
	
	/**
	 * Constructor para la clase de test. Inicializa las opciones para los asserts.
	 */
	function Test(){
		assert_options(ASSERT_ACTIVE, 1);
		assert_options(ASSERT_WARNING, 0);
		assert_options(ASSERT_QUIET_EVAL, 1);
		assert_options(ASSERT_CALLBACK, 'my_assert_handler');
	}
	
	/**
	 * Funcion que ejecuta batería de test relacionados con el acceso a base de datos según el
	 * driver definido en el archivo de configuración config.php
	 */
	function test_BaseDatos(){
		echo "<h2>Test de base de datos</h2>";
		$b=ORM_DRIVER_BD;
		$bd=new $b();
		
		$test=array();
		
		$i=0;
		$test[$i]['nombre']='_conectar';
		$test[$i]['r']=assert($bd->_conectar());
		$i++;
		$test[$i]['nombre']='_desconectar';
		$test[$i]['r']=assert($bd->_desconectar());
		$i++;
		$test[$i]['nombre']='_ejecuta';
		$test[$i]['r']=assert($bd->_ejecuta("SELECT * FROM usuarios"));
		$i++;
		
		$this->mostrarResultadosTests($test, "test_BaseDatos");
	}
	
	/**
	 * Función que muestra el resultado del array de tests pasados por parámetro.
	 * @param array $test El array de test realizados.
	 * @param string $nombreFuncion El nombre de la función que realizó los test.
	 */
	function mostrarResultadosTests($test, $nombreFuncion){
		echo "<h2>Test para '".$nombreFuncion."'";
		$testPasados=true;
		$iTestOk=0;
		$iTestTotal=count($test);
		foreach($test as $t){
			echo "<p>Test ".$t['nombre'].": ";
			if($t['r']){
				echo "Ok!";
				$iTestOk++;
			}else{
				echo "Error!";
				$testPasados=false;
			}
			echo "</p>";
		}
		if($testPasados){
			echo "<h3>".$iTestOk."/".$iTestTotal." los test de '".$nombreFuncion."' se han pasado satisfactoriamente.</h3>";
		}else{
			echo "<h3>Hay algún test, ".$iTestOk."/".$iTestTotal.", que no se ha pasado correctamente.</h3>";
		}
	}
	
	function test_FindCampoBy(){
		$modelo=new Usuario();
		echo "<br><br>Hago findCampoBy()<br><br>";
		
		$u=$modelo->findBy('id', 1, 'login');
		echo "<br>";
		
		$u=$modelo->findBy('UsuariosTipo.id', 1, 'login');
		echo "<br>";
		
		$u=$modelo->findBy('id', 1, 'UsuariosTipo.id');
		echo "<br>";
		
	}
	
	function test_FindBy(){
		$modelo=new Usuario();
		echo "<br><br>Hago findBy()<br><br>";
		
		$u=$modelo->findBy('id', 1);
		echo "<br>";
		
		$u=$modelo->findBy('UsuariosTipo.id', 1, 'id');
		echo "<br>";
		
		$u=$modelo->findBy('UsuariosTipo.id', 1, array('id', 'UsuariosTipo.id'));
		echo "<br>";
		
		$u=$modelo->findBy('UsuariosTipo.id', 1, null, 'login ASC');
		echo "<br>";
		
		$u=$modelo->findBy('UsuariosTipo.id', 1, null, 'login ASC', '3');
		echo "<br>";
		
		$u=$modelo->findBy('UsuariosTipo.id', 1, null, 'login ASC', '2, 3');
		echo "<br>";
		
		$u=$modelo->findBy('UsuariosTipo.id', 1, 'pass', 'login ASC', '2, 3');
		echo "<br>";
	}
	
	function test_Find(){
		$modelo=new Usuario();

		echo "<br><br>Hago find()<br><br>";
		$campos=array('id', 'UsuariosTipo.id');
		
		$condiciones=
		array(
			'OR'=>
			array(
				array('id'=>2, 'UsuariosTipo.id'=>1), 
				array('id'=>1, 'UsuariosTipo.id'=>2)
				)
			);
		$condiciones=
		array(
			array('OR'=>
				array('id'=>2, 'UsuariosTipo.id'=>1)
				),
			array('OR'=>
				array('id'=>1, 'UsuariosTipo.id !='=>2)
				),
			array('id !='=>10, 'UsuariosTipo.id LIKE'=>"%nada%"),
			array('id ='=>10, 'UsuariosTipo.id NOT LIKE'=>12)
			);
//		$condiciones=
//		array(
//			array('id'=>2, 'UsuariosTipo.id'=>1), 
//			array('id'=>1, 'UsuariosTipo.id'=>2)
//			);
//		$condiciones=
//		array('OR'=>
//			array('id'=>2, 'UsuariosTipo.id'=>1)
//			);
//		$condiciones=array('id'=>2, 'UsuariosTipo.id'=>1);
		$u=$modelo->find($condiciones, $campos);
		echo "<br>";
	}
	
	function test_insertar(){
		$modelo=new Usuario();
		
		$datos=array('usuarios_tipo_id'=>2, 'login'=>'pepe', 'pass'=>'666');
		
		$i=0;
		
		$test[$i]['nombre']='_save';
		$test[$i]['r']=assert($modelo->save($datos));
		$i++;
		
		$test[$i]['nombre']='_ultimoid';
		$ultimoId=$modelo->getId();
		$test[$i]['r']=assert($ultimoId>0);
		$i++;
		
		$u=$modelo->findBy('id', $ultimoId);
		$test[$i]['nombre']='_login correcto';
		$test[$i]['r']=assert($u[0]['Usuario']['login']=="pepe");
		$i++;
		
		$test[$i]['nombre']='_usuarios_tipo_id correcto';
		$test[$i]['r']=assert($u[0]['Usuario']['usuarios_tipo_id']==2);
		$i++;
		
		$test[$i]['nombre']='_pass correcto';
		$test[$i]['r']=assert($u[0]['Usuario']['pass']=="666");
		$i++;
		
		$test[$i]['nombre']='_eliminar registro';
		$test[$i]['r']=assert($modelo->delete());
		$i++;
		
		$this->mostrarResultadosTests($test, "test_insertar");
	}
	
	/**
	 * Test que realiza los siguientes pasos:
	 * 		- Inserta un nuevo registro y comprueba si se inserto bien.
	 * 		- Se actualiza el campo 'login' y se comprueba
	 * 		- Se actualiza el campo 'pass' pasandole el id en el array de datos.
	 * 		- Eliminamos el registro pasandole el id.
	 */
	function test_actualizar(){
		$modelo=new Usuario();
		
		$datos=array('usuarios_tipo_id'=>2, 'login'=>'pepe', 'pass'=>'666');
		
		$i=0;
		
		$test[$i]['nombre']='_save';
		$test[$i]['r']=assert($modelo->save($datos));
		$i++;
		
		$test[$i]['nombre']='_ultimoid';
		$ultimoId=$modelo->getId();
		$test[$i]['r']=assert($ultimoId>0);
		$i++;
		
		$u=$modelo->findBy('id', $ultimoId);
		$test[$i]['nombre']='_login correcto';
		$test[$i]['r']=assert($u[0]['Usuario']['login']=="pepe");
		$i++;
		
		$test[$i]['nombre']='_usuarios_tipo_id correcto';
		$test[$i]['r']=assert($u[0]['Usuario']['usuarios_tipo_id']==2);
		$i++;
		
		$test[$i]['nombre']='_pass correcto';
		$test[$i]['r']=assert($u[0]['Usuario']['pass']=="666");
		$i++;
		
		$datos=null;
		$datos['login']='manolo';
		$test[$i]['nombre']='_update';
		$test[$i]['r']=assert($modelo->save($datos));
		$i++;
		
		$u=$modelo->findBy('id', $ultimoId);
		$test[$i]['nombre']='_login actualizado';
		$test[$i]['r']=assert($u[0]['Usuario']['login']=="manolo");
		$i++;
		
		$datos=null;
		$datos['pass']='6969';
		$datos['id']=$ultimoId;
		$modelo->id=null;
		$test[$i]['nombre']='_update con id en datos';
		$test[$i]['r']=assert($modelo->save($datos));
		$i++;
		
		$u=$modelo->findBy('id', $ultimoId);
		$test[$i]['nombre']='_pass actualizado';
		$test[$i]['r']=assert($u[0]['Usuario']['pass']=="6969");
		$i++;
		
		$test[$i]['nombre']='_eliminar registro pasandole id';
		$test[$i]['r']=assert($modelo->delete($ultimoId));
		$i++;
		
		$this->mostrarResultadosTests($test, "test_actualizar");
	}
	
	/**
	 * 
	 * @see http://php.net/manual/en/function.assert.php
	 * @param string $file
	 * @param int $line
	 * @param string $code
	 */
	function my_assert_handler($file, $line, $code){
	    echo "<hr>Assertion Failed:
	        File '$file'<br />
	        Line '$line'<br />
	        Code '$code'<br /><hr />";
	}
}
?>