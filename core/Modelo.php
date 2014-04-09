<?php

/**
 * Clase para modelar un objeto de una tabla de base de datos.
 * 
 * @name Modelo
 * @author lobo mloobo@gmail.com
 *
 */
class Modelo{
	
	/**
	 * El nombre de la tabla que usará este modelo.
	 * Se puede establecer su valor al declarar la clase que herede de Modelo.
	 * @var string
	 */
	var $tabla="";
	
	/**
	 * El nombre del modelo. Por defecto cogerá el nombre de la clase.
	 * Se puede establecer su valor al declarar la clase que herede de Modelo.
	 * @var string
	 */
	var $nombre='';
	
	/**
	 * El identificador del registro sobre el que se está actuando.
	 * @var int
	 */
	var $id=0;
	
	/**
	 * El nombre del campo, de la tabla que se modela, que es clave primaria. 
	 * @var string
	 */
	var $primaryKey='';
	
	/**
	 * array(
	 * 		'Mimodelo11_1'=>
	 * 		array(
	 * 			'className'=>'Mimodelo11_1',
	 * 			'foreignKey'=>'id'
	 * 		),
	 * 		'Mimodelo11_2'=>
	 * 		array(
	 * 			'className'=>'Mimodelo11_2',
	 * 			'foreignKey'=>'cualquier_campo'
	 * 		)
	 * )
	 * @var array
	 */
	var $asoc11=array();
	
	/**
	 * array(
	 * 		'Mimodelo1N_1'=>
	 * 		array(
	 * 			'className'=>'Mimodelo1N_1',
	 * 			'foreignKey'=>'nombre_campo_foraneo_Mimodelo1N_1'
	 * 		)
	 * )
	 * @var array
	 */
	var $asoc1N=array();
	
	/**
	 * Establece si se añaden los JOINS 1-1 de las posibles relaciones con tablas.
	 * @var bool
	 */
	var $ocultarAsoc11=false;
	
	/**
	 * Establece si se añaden los JOINS 1-N de las posibles relaciones con tablas.
	 * @var bool
	 */
	var $ocultarAsoc1N=false;
	
	/**
	 * El objeto que usará el modelo para acceder a la base de datos.
	 * @var BaseDatos
	 */
	var $bd=null;
	
	/**
	 * Constructor.
	 * Carga el objeto de gestion de base de datos en funcion de los especificado
	 * en los parámetros de configuracion.
	 * Establece el nombre del modelo usando el nombre de la clase en caso de que no se haya
	 * especificado ya.
	 * Como nombre de tabla que usará este modelo, usará el nombre de la clase (en minúsculas)
	 * seguido de una 's'.
	 */
	function Modelo(){
		$this->_loadObjetoBd();
		if(empty($this->nombre)){
			$this->nombre=ucfirst(get_class($this));
		}
		if(empty($this->tabla)){
			$this->tabla=get_class($this).'s';
		}
		if(empty($this->primaryKey)){
			$this->_setPrimaryKey();
		}
	}
	
	/**
	 * Funcion que obtiene, si lo hay, un operador binario de la cadena pasada por parámetro
	 * @param string $key Se pasa por referencia. Ejs. "id !=", "id =", "id", "nombre NOT LIKE", "pass LIKE"
	 * @return string Por defecto, si no lleva operador, devuelve el operador "=".
	 */
	function _getOperadorBinarioCondicionSimple($key){
		$trozosOperador=split(" ", $key);
		$operador='';
		$t=count($trozosOperador);
		if($t>1){
			for($i=1;$i<$t;$i++){
				$operador.=$trozosOperador[$i];
				if($i<($t-1)){
					$operador.=" ";
				}
			}
			$key=str_replace(" ".$operador, "", $key);
		}else{
			$operador='=';
		}
		return $operador;
	}
	
	/**
	 * Funcion privada para la carga del objeto de gestion de datos.
	 * En funcion del parámetro de configuración ORM_DRIVER_BD se elegirá
	 * uno u otro driver.
	 */
	function _loadObjetoBd(){
		switch(ORM_DRIVER_BD){
			case "MySQL":
				$this->bd=new MySQL();
				break;
			case "postgresql":
				break;
			case "oracle":
				break;
			case "mssqlserver":
				break;
		}
		if(is_null($this->bd)){
			$e=new Excepcion("Modelo", "_loadObjetoBd", "El objeto de base de datos no se ha podido cargar para el driver '".ORM_DRIVER_BD."'.");
			echo $e->__toString();
		}
	}
	
	/**
	 * Funcion privada para modificar la query recibida en funcion de las asociaciones
	 * que tenga este modelo de datos.
	 * En realidad, si tiene asociaciones (y no se ha indicado que se oculte), se añade 
	 * una posición al array para cada JOIN que haya que hacer para los modelos asociados.
	 * @param array $query Se recibe por referencia.
	 */
	function _setCondicionesAsoc($query){
		$this->_setCondicionesAsoc11(&$query);
		$this->_setCondicionesAsoc1N(&$query);
	}
	
	/**
	 * Funcion privada para modificar la query recibida en función de las asociaciones 1-1 que tenga el modelo.
	 * @param array $query Se recibe por referencia.
	 */
	function _setCondicionesAsoc11($query){
		if(!$this->ocultarAsoc11 && !empty($this->asoc11)){
			foreach($this->asoc11 as $j){
				$modelo=new $j['className']();
				$tabla=$modelo->getTabla();
				$nombreModelo=$modelo->getNombre();
				$primaryKey=$modelo->getPrimaryKey();
				$foreignKey=$j['foreignKey'];
				$query['joins'][$nombreModelo]="LEFT JOIN ".$tabla." as ".$nombreModelo." on 
					(".$this->nombre.".".$foreignKey."=".$nombreModelo.".".$primaryKey.")";
			}
		}
	}
	
	/**
	 * Funcion privada para modificar la query recibida en función de las asociaciones 1-N que tenga el modelo.
	 * @param array $query Se recibe por referencia.
	 */
	function _setCondicionesAsoc1N($query){
		if(!$this->ocultarAsoc1N && !empty($this->asoc1N)){
			foreach($this->asoc1N as $j){
				$modelo=new $j['className']();
				$tabla=$modelo->getTabla();
				$nombreModelo=$modelo->getNombre();
				$foreignKey=$j['foreignKey'];
				$query['joins'][$nombreModelo]="INNER JOIN ".$tabla." as ".$nombreModelo." on 
					(".$this->nombre.".".$this->primaryKey."=".$nombreModelo.".".$foreignKey.")";
			}
		}
	}
	
	/**
	 * Funcion que permite construir la parte del WHERE de una consulta. Esta función permite composiciones como:
	 *   * a=b
	 *   * a=b AND c=d
	 *   * a=b AND c=d AND (e=f OR g=h)
	 *   * a=b AND c=d OR e=f
	 *   * a=b AND c=d AND (e=f OR g=h) AND i=j
	 * @param string $queryCondicion Se pasa por referencia
	 * @param array $condiciones
	 * @param string $conector Conectores permitidos: AND, OR
	 */
	function _setCondiciones($queryCondicion, $condiciones, $conector="AND"){
		$contectoresPermitidos=array('AND', 'OR');
		if(!in_array($conector, $contectoresPermitidos)){
			$e=new Excepcion("Modelo", "_setCondiciones", "No se reconoce el contector SQL '".$contector."'.");
			echo $e->__toString();
			return false;
		}
//		if(DEBUG){
//			echo print_r($condiciones);
//		}
		
		list($key, $val)=each($condiciones);
		reset($condiciones);
		
		switch(strtolower($key)){
			case "or":
				$this->_setCondiciones(&$queryCondicion, $val, $key);
			break;
			
			default:
				$this->_setCondicionSimple(&$queryCondicion, &$condiciones, $conector);
			break;
		}
	}
	
	/**
	 * Funcion privada para la modificación de la condición de una query en
	 * función del campo $valorBy que se recibe. Si es un número no se le pondrán
	 * comillas en la sentencia.
	 * @param array &$query
	 * @param string $by
	 * @param string $valorBy
	 */
	function _setCondicionFindBy($query, $by, $valorBy){
		$pos=strpos($by, $this->nombre.".");
		$tienePunto=strpos($by, ".");
		if($pos===false && $tienePunto===false){
			$query['condicion'].=$this->nombre.'.';	
		}
		if(is_numeric($valorBy)){
			$query['condicion'].=$by."=".$valorBy;
		}else{
			$query['condicion'].=$by."='".$valorBy."'";
		}
	}
	
	/**
	 * Compone una cadena para hacer una comparación A=B para wl WHERE de una sentencia SQL.
	 * @param string $queryCondicion Se pasa por referencia.
	 * @param string $key
	 * @param int|string $val
	 * @param string $conector
	 * @param int $i
	 * @param int $t
	 */
	function _setCondicionIgualA($queryCondicion, $key, $val, $conector, $i, $t, $operador){
		$tienePunto=strpos($key, ".");
		if($tienePunto===false){
			$queryCondicion.=$this->nombre.'.';
		}
		$queryCondicion.=$key.' '.$operador." ";
		if(is_numeric($val)){
			$queryCondicion.=$val;
		}else{
			$queryCondicion.="'".$val."'";
		}
		
		if($i<($t-1)){
			$queryCondicion.=' '.$conector.' ';
		}
	}
	
	/**
	 * Funcion que compone la estrutura para una condicion simple para un WHERE.
	 * Esta función es privada para uso explusivo de _setCondiciones()
	 * @param string $queryCondicion Se pasa por referencia.
	 * @param array $condiciones
	 * @param string $conector
	 */
	function _setCondicionSimple($queryCondicion, $condiciones, $conector){
		$queryCondicion.='( ';
		$i=0;
		$t=count($condiciones);
		while(list($key, $val)=each($condiciones)){
			if(is_array($val)){
				$this->_setCondiciones(&$queryCondicion, $val, "AND");
				if($i<($t-1)){
					$queryCondicion.=' '.$conector.' ';
				}
			}else{
				$key=trim($key); $val=trim($val);
				$operador=$this->_getOperadorBinarioCondicionSimple(&$key);
				$this->_setCondicionIgualA(&$queryCondicion, $key, $val, $conector, $i, $t, $operador);
			}
			$i++;
		}
		$queryCondicion.=' )';
	}
	
	/**
	 * Función privada para establecer la clave primaria de la tabla para este
	 * modelo.
	 * Sólo la establece si ha sido establecida previamente.
	 */
	function _setPrimaryKey(){
		if(is_null($this->bd)){
			$e=new Excepcion("Modelo", "_setPrimaryKey", "El objeto de base de datos no está cargado.");
			echo $e->__toString();
		}
		if(empty($this->primaryKey)){
			$this->primaryKey=$this->bd->getPrimaryKeyTabla($this->tabla);
		}
	}
	
	/**
	 * Funcion que elimina el registro cuyo id se puede pasar por parámetro. En caso
	 * que no se pase identificador se coge el id que tiene el modelo en su parámetro de
	 * clase $id.
	 * @param int $id Identificador de clave primaria a eliminar.
	 * @return bool TRUE si todo fue bien, FALSE en caso contrario.
	 */
	function delete($id=null){
		if(empty($id)){
			$id=$this->id;
		}
		if(empty($id)){
			$e=new Excepcion("Modelo", "delete", "No se ha podido obtener el identificador de clave primaria para eliminar.");
			echo $e->__toString();
			return false;
		}
		return $this->bd->eliminar($this, $id);
	}
	
	/**
	 * Funcion que realiza una búsqueda de datos en el modelo, en función de los
	 * parámetros recibidos.
	 * @param array $condicion La condición SQL que irá en la clausula WHERE.
	 * @param array $campos Los campos que se quieres recuperar del modelo, o de los modelos a asociados.
	 * @param string $order El orden en que se devolverán los datos. Ej. Mimodelo.fecha ASC, Miotromodelo.nombre DESC
	 * @param string $group
	 * @param string $limit
	 * @return array El array de datos obtenidos de la búsqueda.
	 */
	function find($condiciones, $campos, $order=null, $group=null, $limit=''){
		$query=array();
		$query['condicion']='';
		$this->_setCondiciones(&$query['condicion'], $condiciones);
		$query['campos']=$campos;
		$query['order']=$order;
		$query['group']=$group;
		$query['limit']=$limit;
		$this->_setCondicionesAsoc(&$query);
		
		return $this->bd->read($this, $query);
	}
	
	/**
	 * Funcion que realiza una búsqueda de datos en el modelo. Busca las 
	 * coincidencias del campo $by con el valor $valorBy.
	 * Devuelve los campos indicados en $campos con el $orden pasado.
	 * @param string $by El nombre del campo por el que se buscará.
	 * @param int|string $valorBy El valor que deberá tener el campo por el que buscará.
	 * @param array $campos Los campos que se quieres recuperar del modelo, o de
	 * los modelos a asociados.
	 * @param string $order El orden en que se devolverán los datos. 
	 * 		Ej. Mimodelo.fecha ASC, Miotromodelo.nombre DESC
	 * @return array El array de datos obtenidos de la búsqueda
	 */
	function findBy($by, $valorBy, $campos=null, $order='', $limit=''){
		$query=array();
		$this->_setCondicionFindBy(&$query, $by, $valorBy);
		$query['campos']=$campos;
		$query['order']=$order;
		$query['limit']=$limit;
		$this->_setCondicionesAsoc(&$query);
		
		return $this->bd->read($this, $query);
	}
	
	/**
	 * Funcion que realiza una búsqueda de datos en el modelo. Busca las 
	 * coincidencias del campo $by con el valor $valorBy.
	 * Devuelve el campo indicado en $campo.
	 * @param string $by El nombre del campo por el que se buscará.
	 * @param int|string $valorBy El valor que deberá tener el campo por el que buscará.
	 * @param string $campo El nombre del campo cuyo valor se devolverá.
	 * @return int|string El valor del campo para el registro encontrado.
	 */
	function findCampoBy($by, $valorBy, $campo){
		$query=array();
		$this->_setCondicionFindBy(&$query, $by, $valorBy);
		$query['campos']=$campo;
		if(is_array($campo)){
			$query['campos']=array($campo);
		}
		$this->_setCondicionesAsoc(&$query);
		
		$r=$this->bd->read($this, $query);
		
		if($r){
			list($key, $val)=each($r[0]);
			$campo=str_replace($key.".", "", $campo);
			if(!isset($val[$campo])){
				$e=new Excepcion("Modelo", "findCampoBy", "No se ha encontrado el campo cuyo valor desea devolver.");
				echo $e->__toString();
				return null;
			}
			return $val[$campo];
		}
		return null;
	}
	
	/**
	 * Devuelve el identificador del registro sobre el que se está trabajando.
	 * @return int
	 */
	function getId(){
		return $this->id;
	}
	
	/**
	 * Devuelve el nombre del modelo.
	 * @return string
	 */
	function getNombre(){
		return $this->nombre;
	}
	
	/**
	 * Devuelve la clave primaria del modelo
	 * @return mixed
	 */
	function getPrimaryKey(){
		return $this->primaryKey;
	}
	
	/**
	 * Devuelve el nombre de la tabla que utiliza el modelo.
	 * @return string
	 */
	function getTabla(){
		return $this->tabla;
	}
	
	/**
	 * Funcion que establece si se quiere incluí los JOINS para las posibles asociaciones 1-1.
	 * @param bool $bool
	 */
	function ocultarAsoc11($bool){
		$this->ocultarAsoc11=$bool;
	}
	
	/**
	 * Funcion que establece si se quiere incluí los JOINS para las posibles asociaciones 1-N.
	 * @param bool $bool
	 */
	function ocultarAsoc1N($bool){
		$this->ocultarAsoc1N=$bool;
	}
	
	/**
	 * Funcion para hacer una consulta a mano.
	 * @param string $query
	 */
	function query($query){
		return $this->bd->select($query);
	}
	
	/**
	 * Inserta o actualiza datos relacionados con el modelo en la base de datos.
	 * Si el objeto tiene valor en el parámetro $this->id, o el array de datos pasado por parámetro
	 * a esta función contiene 'nombreCampoPrimaryKey'=>valor, se realizará un UPDATE en lugar de INSERT.
	 * @param array $datos Array de nombres de campos con los valores correspondientes.
	 * @return bool TRUE si fue bien, FALSE caso de error.
	 */
	function save($datos){
		if(empty($datos)){
			$e=new Excepcion("Modelo", "save", "No se han recibido datos para guardar en base de datos.");
			echo $e->__toString();
			return false;
		}
		$query=array();
		$query['campos']=$datos;
		$nombreCampoPrimaryKey=$this->getPrimaryKey();
		if(!empty($this->id) || isset($datos[$nombreCampoPrimaryKey])){
			//se quiere ACTUALIZAR.
			if(empty($this->id)){
				$this->id=$datos[$nombreCampoPrimaryKey];
			}
			
			unset($datos[$nombreCampoPrimaryKey]);
			
			if($this->bd->actualizar($this, $query)){
				return true;
			}else{
				$e=new Excepcion("Modelo", "save", "No se ha podido actualizar el registro con id=".$this->id.".");
				echo $e->__toString();
			}
		}else{
			//se quiere INSERTAR
			if($this->bd->insertar($this, $query)){
				$this->id=$this->bd->getUltimoIdInsertado();
				return true;
			}else{
				$e=new Excepcion("Modelo", "save", "No se ha podido crear un nuevo registro.");
				echo $e->__toString();
			}
		}
		
		return false;
	}
	
	/**
	 * Establece el objeto de tipo BaseDatos para el modelo.
	 * @param BaseDatos $bd
	 */
	function setBd($bd){
		$this->bd=$bd;
	}
}

?>