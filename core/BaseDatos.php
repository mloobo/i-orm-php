<?php

/**
 * Clase para la gestión de la base de datos.
 * De esta clase heredarán el resto (MySQL, PostGres, etc)
 * 
 * @name BaseDatos
 * @author lobo (mloobo@gmail.com)
 *
 */
class BaseDatos{
	
	/**
	 * private
	 * Array que contendrá los nombres de los modelos asociados al modelo
	 * al que pertenece este objeto de base de datos.
	 * @var array
	 */
	var $_joins=array();

	var $nombre='';
	var $host='';
	var $usuario='';
	var $pass='';
	var $bd='';
	
	/**
	 * El link resource a la base de datos.
	 * @var mixed
	 */
	var $linkbd=null;
	
	/**
	 * El objeto modelo que está gestionando el objeto de esta clase.
	 * @var Modelo
	 */
	var $modelo='';

	/**
	 * El valor del identificador que se está manejando, actualmente, en las
	 * operaciones. Normalmente, es el ultimo identficador creado en base de datos.
	 * @var int
	 */
	var $lastId=0;

	/**
	 * Constructor.
	 * Coge los datos de conexion del archivo de configuracion config.php.
	 */
	function BaseDatos(){
		$this->host=ORM_HOST;
		$this->usuario=ORM_USUARIO;
		$this->pass=ORM_PASS;
		$this->bd=ORM_BD;
	}

	/**
	 * Funcion que realiza la conexion a la base de datos.
	 * @return bool
	 */
	function _conectar(){
		return true;
	}
	
	/**
	 * Funcion que realiza la desconexion a la base de datos.
	 * @return bool
	 */
	function _desconectar(){
		return true;
	}
	
	/**
	 * Ejecuta una sentencia SQL sin obtener datos.
	 * Además, si se inserta, actualiza el parámetro de clase $lastId con
	 * el último id insertado.
	 * @param string $sql
	 * @return bool TRUE si fue bien. FALSE en caso contrario.
	 */
	function _ejecuta($sql){
		if($this->_conectar()){
			$this->_desconectar();
		}
		return false;
	}

	function _getError(){
		return "";
	}

	/**
	 * Funcion que se encarga de pasar un array de campos a una estructura de consulta SQL.
	 * @param array &$query
	 */
	function _tratarCampos($query){
		$campos=$query['campos'];
		$this->_joins=array();
		if(empty($campos)){
			if(isset($query['joins'])){
				$campos=$this->modelo->nombre.'.*, ';
				$i=0; $t=count($query['joins']);
				while(list($key, $join)=each($query['joins'])){
					$campos.=$key.'.*';
					if($i<($t-1)){
						$campos.=', ';
					}
					$this->_joins[]=$key;
					$i++;
				}
			}else{
				$campos='*';
			}
		}else{
			if(is_array($campos)){
				$auxc=''; $i=0; $t=count($campos);
				foreach($campos as $c){
					$pos=strpos($c, $this->modelo->nombre.".");
					$tienePunto=strpos($c, ".");
					if($pos===false && $tienePunto===false){
						$auxc.=$this->modelo->nombre.'.'.$c;
					}else{
						$auxc.=$c;
					}
					if($i<($t-1)){
						$auxc.=', ';
					}
					$i++;
				}
				$campos=$auxc;
			}else{
				//aqui se llega cuando es un único campo el que se pide
				$pos=strpos($campos, $this->modelo->nombre.".");
				$tienePunto=strpos($campos, ".");
				if($pos===false && $tienePunto===false){
					$campos=$this->modelo->nombre.'.'.$campos;
				}else{
					$campos=$campos;
				}
			}
		}
		$query['campos']=$campos;
	}
	

	/**
	 * Funcion que se encarga de actualizar un registro en la base de datos.
	 * @param Modelo $modelo
	 * @param string $query
	 * @return bool TRUE si fue bien, FALSE en caso contrario.
	 */
	function actualizar($modelo, $query){
		
	}
	
	/**
	 * Funcion que se encarga de eliminar un registro en la base de datos.
	 * @param Modelo $modelo
	 * @param int $id
	 * @return bool TRUE si fue bien, FALSE en caso contrario.
	 */
	function eliminar($modelo, $id){
		
	}
	
	/**
	 * Se encarga de comprobar si existe el campo 'created', de tipo DATE o
	 * DATETIME, en la tabla de la base de datos.
	 * @return bool Devuelve TRUE si existe el campo 'created' en la tabla
	 * de la base de datos
	 */
	function existeCampoCreated(){
		return false;
	}
	
	/**
	 * Se encarga de comprobar si existe el campo 'modified', de tipo DATE o
	 * DATETIME, en la tabla de la base de datos.
	 * @return bool Devuelve TRUE si existe el campo 'modified' en la tabla
	 * de la base de datos
	 */
	function existeCampoModified(){
		return false;
	}

	/**
	 * Funcion que devuelve el ultimo identificador insertado en la base de datos.
	 * @return int
	 */
	function getUltimoIdInsertado(){
		return 0;
	}

	/**
	 * Funcion que devuelve el nombre del campo que actua como PRIMARY KEY en la
	 * tabla, pasada por parámetro, de la base de datos.  
	 * @param string $tabla
	 * @return string
	 */
	function getPrimaryKeyTabla($tabla){
		return 'id';
	}
	
	/**
	 * Funcion que realiza un INSERT en la base de datos.
	 * @param Modelo $modelo
	 * @param array $query
	 * @return bool TRUE si se ejecutó bien. FALSE en caso contrario.
	 */
	function insertar($modelo, $query){
		
	}

	/**
	 * Funcion que recibe un modelo y una query a ejecutar.
	 * Compone los JOINS que tenga asociado el modelo.
	 * @param Modelo $modelo
	 * @param array $query El array de información que compone las
	 * partes de una query.
	 * @return array El array de datos obtenido al ejecutar la query.
	 */
	function read($modelo, $query){
		return null;
	}

	/**
	 * Funcion que ejecuta una query de SQL.
	 * @param string $sql
	 * @return array El array de datos obtenido de la query. Puede ser vacío.
	 */
	function select($sql){
		return null;
	}
}

?>