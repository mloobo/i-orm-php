<?php

/**
 * Driver para poder gestionar bases de datos de tipo MySQL.
 * 
 * @name MySQL
 * @author lobo (mloobo@gmail.com)
 *
 */
class MySQL extends BaseDatos{
	
	/**
	 * Constructor.
	 */
	function MySQL(){
		parent::BaseDatos();
		$this->nombre="MySQL";
	}

	
	/**
	 * (non-PHPdoc)
	 * @see i-orm-php/BaseDatos#_conectar()
	 */
	function _conectar(){
		$this->linkbd= mysql_connect($this->host, $this->usuario, $this->pass);
		if(!$this->linkbd){
			echo mysql_error().'<br />';
			return false;
		}
		if(!mysql_select_db($this->bd, $this->linkbd)){
			$this->_getError();
			return false;
		}
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see i-orm-php/BaseDatos#_ejecuta($sql)
	 */
	function _ejecuta($sql){
		if($this->_conectar()){
			if(DEBUG){
				echo "<br>Ejecuta: ".$sql."<br />";
			}
			$r=mysql_query($sql, $this->linkbd);
			$this->lastId=mysql_insert_id($this->linkbd);
			$this->_desconectar();
			return $r;
		}
		return false;
	}

	/**
	 * (non-PHPdoc)
	 * @see i-orm-php/BaseDatos#_desconectar()
	 */
	function _desconectar(){
		return mysql_close($this->linkbd);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see i-orm-php/BaseDatos#_getError()
	 */
	function _getError(){
		echo '<p class="error_mysql">'.mysql_error($this->linkbd).'</p>';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see /core/BaseDatos#actualizar($modelo, $query)
	 */
	function actualizar($modelo, $query){
		$campos=$query['campos'];
		
		$q='UPDATE ';
		$q.=$modelo->getTabla();
		$q.=' SET ';
		$i=0; $t=count($campos);
		while(list($nombre, $valor)=each($campos)){
			$q.=$nombre.' = ';
			if(is_numeric($valor)){
				$q.=$valor;
			}else{
				$q.="'".$valor."'";
			}
			if($i<($t-1)){
				$q.=', ';
			}
			$i++;
		}
		$q.=' WHERE '.$modelo->getPrimaryKey().' = '.$modelo->id.';';
		
//		echo $q;
		
		return $this->_ejecuta($q);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see i-orm-php/BaseDatos#eliminar($modelo, $id)
	 */
	function eliminar($modelo, $id){
		$q='DELETE FROM '.$modelo->getTabla();
		$q.=' WHERE '.$modelo->getPrimaryKey().'='.$id.';';
		
		return $this->_ejecuta($q);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see i-orm-php/BaseDatos#getPrimaryKeyTabla($tabla)
	 */
	function getPrimaryKeyTabla($tabla){		
		$id='id';
		// la tabla "INFORMATION_SCHEMA.KEY_COLUMN_USAGE" está disponible a partir de MySQL 5.0.6
		if(ORM_DRIVER_BD_MYSQL_VERSION<5.0){
			return $id;
		}
		$sql="SELECT COLUMN_NAME
		FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
		WHERE TABLE_NAME = '".$tabla."'";
		
		$this->_conectar();
		$result = mysql_query($sql, $this->linkbd);
		if(mysql_errno($this->linkbd)!=0){
			if(DEBUG){
				$this->_getError().'<br />';
			}
		}else{
			$row = $this->mysql_fetch_alias_array($result);
			mysql_free_result($result);
			$id=$row['KEY_COLUMN_USAGE']['COLUMN_NAME'];
		}
		$this->_desconectar();
		
		return $id;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see i-orm-php/BaseDatos#getUltimoIdInsertado()
	 */
	function getUltimoIdInsertado(){
		return $this->lastId;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see i-orm-php/BaseDatos#insertar($modelo, $query)
	 */
	function insertar($modelo, $query){
		$campos=$query['campos'];
		$nombresCampos='';
		$valoresCampos='';
		
		$q='INSERT INTO ';
		$q.=$modelo->getTabla();
		$i=0; $t=count($campos);
		while(list($nombre, $valor)=each($campos)){
			$nombresCampos.=$nombre;
			if(is_numeric($valor)){
				$valoresCampos.=$valor;
			}else{
				$valoresCampos.="'".$valor."'";
			}
			if($i<($t-1)){
				$nombresCampos.=', ';
				$valoresCampos.=', ';
			}
			$i++;
		}
		$q.='('.$nombresCampos.') ';
		$q.='VALUES ('.$valoresCampos.');';
		
		//echo $q;
		
		return $this->_ejecuta($q);
	}

	/**
	 * @see http://es.php.net/manual/en/function.mysql-fetch-array.php#92328
	 * @param mysql result $result
	 * @return array
	 */
	function mysql_fetch_alias_array($result)	{
		if (!($row = mysql_fetch_array($result))){
			return null;
		}

		$assoc = Array();
		$rowCount = mysql_num_fields($result);
		$i=0;
		for ($idx = 0; $idx < $rowCount; $idx++){
			$table = mysql_field_table($result, $idx);
			$field = mysql_field_name($result, $idx);
			$assoc[$table][$field] = $row[$idx];
		}
			
		return $assoc;
	}

	/**
	 * (non-PHPdoc)
	 * @see i-orm-php/BaseDatos#select($sql)
	 */
	function select($sql){
		$result=null;
		$datos=array();
		if($this->_conectar()){
			if(DEBUG){
				echo "<br>Query: ".$sql."<br />";
			}
			$result = mysql_query($sql, $this->linkbd);
			if(!$result){
				if(mysql_errno($this->linkbd)!=0){
					$this->_getError(); 
				}
				return null;
			}
			$this->_desconectar();
			while ($row = $this->mysql_fetch_alias_array($result)) {
				$datos[]=$row;
			}
			mysql_free_result($result);
		}
		return $datos;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see i-orm-php/BaseDatos#read($modelo, $query)
	 */
	function read($modelo, $query){
		$this->modelo=$modelo;
		
		$condicion=$query['condicion'];
		$joins=null;
		if(isset($query['joins'])){
			$joins=$query['joins'];
		}
		$this->_tratarCampos(&$query);
		$campos=$query['campos'];

		$q='SELECT ';
		$q.=$campos;
		$q.=' FROM '.$this->modelo->tabla.' as '.$this->modelo->nombre;
		if(!empty($joins)){
			foreach($joins as $j){
				$q.=' '.$j;
			}
		}
		if(!empty($condicion)){
			$q.=' WHERE '.($condicion);
		}
		if(!empty($query['order'])){
			$q.=' ORDER BY '.$query['order'];
		}
		if(!empty($query['group'])){
			$q.=' GROUP BY '.$query['order'];
		}
		if(!empty($query['limit'])){
			$q.=' LIMIT '.$query['limit'];
		}

		//echo $q;

		return $this->select($q);
	}
}