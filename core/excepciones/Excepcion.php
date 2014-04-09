<?php
/**
 * Clase general para el tratamiento de excepciones.
 * @author lobo (mloobo@gmail.com)
 *
 */
class Excepcion{
	var $mensaje="";
	var $clase="";
	var $funcion="";
	var $nombre="Excepcion General";
	
	/**
	 * Constructor.
	 * @param string $clase Nombre de la clase donde ocurre la excepci�n.
	 * @param string $funcion Nombre de la funci�n donde ocurre la excepci�n.
	 * @param string $mensaje El mensaje
	 */
	function Excepcion($clase, $funcion, $mensaje="Error general"){
		$this->clase=$clase;
		$this->funcion=$funcion;
		$this->mensaje=$mensaje;
	}
	
	/**
	 * M�todo toString() sobre-escrito.
	 * @Overrride
	 * @return string
	 */
	function __toString(){
		return '<p class="iormphp_excepcion">'.$this->nombre.'; '.$this->clase.'::'.$this->funcion.': '.$this->mensaje.'</p>';
	}
}
?>