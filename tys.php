<?php 
class TysException extends Exception {
	private $_tysOutput = null;
	
	public function __construct(TysOutput $tysOutput){
		parent::__construct($tysOutput->doc->msg, $tysOutput->doc->ret);
		
		$this->_tysOutput = $tysOutput;
	}
	
	public function getTysOutput(){
		return $this->_tysOutput;
	}
}

class TysDocument{
	private $_data = array();
	
	public function __set($name, $value){
		$this->_data[$name] = $value;
	}

	public function __get($name){
		if(!array_key_exists($name, $this->_data))
			$this->_data[$name] = new Ngi_Core_Document;
		return $this->_data[$name];
	}

	public function __isset($name){
		return isset($this->_data[$name]);
	}

	public function __unset($name){
		unset($this->_data[$name]);
	}

	public function toArray(){
		$data = array();
		foreach($this->_data as $name => $value){
			$data[$name] = is_a($value, 'TysDocument') ? $value->dump() : $value;
		}
		return $data;
	}
}

class TysOutput{
	const TYPE_HTML		= 'html';
	const TYPE_JSON		= 'json';
	const TYPE_XML		= 'xml';
	const TYPE_JSONP	= 'jsonp';
	const TYPE_AMF		= 'amf';
	
	const RET_SUCC		= 0;
	const RET_ERROR		= 1;
	
	private static $typeCfgMap = array();
	private static $type = self::TYPE_JSON;
	
	public $doc = null;
	
	public static function initTypeCfg($type, $typeCfg){
		self::$typeCfgMap[$type] = $typeCfg;
	}
	
	public static function getTypeCfg($type){
		return isset(self::$typeCfgMap[$type]) ? self::$typeCfgMap[$type] : null;
	}
	
	public static function getType(){
		return self::$type;
	}
	
	public static function setType($type){
		self::$type = $type;
	}
	
	public function getOutput(){
		return $this->doc->toArray();
	}
	
	public function __construct(){
		$this->doc = new TysDocument();
	}
}

abstract class TysAbstract{
	protected $_output = array();
	
	public static function halt($ret, $msg){
		$tysOutput = new TysOutput();
		$tysOutput->doc->ret = $ret;
		$tysOutput->doc->msg = $msg;
		
		throw new TysException($tysOutput);
	}
	
	public function getTysOutput(){
		$tysOutput = new TysOutput();
		$tysOutput->doc->ret = TysOutput::RET_SUCC;
		$tysOutput->doc->data = $this->_output;
		
		return $tysOutput;
	}
	
	public function run(){
		$this->input();
		$this->process();
	}
	
	protected function checkInput(){
		$args = func_get_args();
		foreach($args as $key){
			$val = Tyi::input($key);
			if( ! isset($val)){
				self::halt(TysOutput::RET_ERROR, "tyi input error, no {$key} param");
			}
		}
	}
	
	abstract protected function input();
	abstract protected function process();
}

