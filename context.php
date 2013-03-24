<?php

class TyiContext
{
	private $_data = array();

	public function __set($name, $value){
		$this->_data[$name] = $value;
	}

	public function __get($name){
		if(!array_key_exists($name, $this->_data))
			$this->_data[$name] = new Ngi_Core_Context;
		return $this->_data[$name];
	}

	public function __isset($name){
		return isset($this->_data[$name]);
	}

	public function __unset($name){
		unset($this->_data[$name]);
	}	
}
