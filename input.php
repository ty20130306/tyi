<?php

abstract class TyiInputHandlerAbstract{
	public function get($name, $default = null){
		$value = $this->_get($name);
		return (!isset($value) && isset($default)) ? $default : $value;
	}

	abstract protected function _get($name);
}

class TyiInputHandlerAmf extends TyiInputHandlerAbstract{
	protected static $_amfData = array();

	public function __construct(){
		if(!is_array(self::$_amfData)){
			$rawData = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents('php://input');
			self::$_amfData = amf_decode($rawData);
			if(!isset(self::$_amfData))
				self::$_amfData = array();      
		}
	}

	protected function _get($name){
		return isset(self::$_amfData[$name]) ? self::$_amfData[$name] : null;
	}
}

class TyiInputHandlerHttp extends TyiInputHandlerAbstract{
	protected function _get($name){
		return isset($_REQUEST[$name]) ? $_REQUEST[$name] : null;
	}
}