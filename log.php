<?php

class TyiLog {
	const RUNTIME_LEVEL_FATAL = 0x01;
	const RUNTIME_LEVEL_ERROR = 0x02;
	const RUNTIME_LEVEL_WARNING = 0x03;
	const RUNTIME_LEVEL_NOTICE = 0x04;
	const RUNTIME_LEVEL_DEBUG = 0x05;
	
	static private $_runtimeLogDir	= '/tmp';
	static private $_actionLogDir	= '/tmp';
	
	static private $_runtimeLevel	= self::RUNTIME_LEVEL_DEBUG;
	
	static public function init($runtimeLogDir, $actionLogDir){
		self::$_runtimeLogDir = $runtimeLogDir;
		self::$_actionLogDir = $actionLogDir;
	}
	
	static public function getRuntimeLevel(){
		return self::$_runtimeLevel;
	}

	static public function setRuntimeLevel($runtimeLevel){
		self::$_runtimeLevel = $runtimeLevel;
	}
	
	static public function runtime($msg, $level){
		if( ! self::seriousEnoughToLog($level)){
			return;
		}
		
		$data = '[' . date('Y-m-d H:i:s') . '][';
		switch($level){
			case self::RUNTIME_LEVEL_FATAL:
				$data .= 'FATAL';
				break;
			case self::RUNTIME_LEVEL_ERROR:
				$data .= 'ERROR';
				break;
			case self::RUNTIME_LEVEL_WARNING:
				$data .= 'WARNING';
				break;
			case self::RUNTIME_LEVEL_NOTICE:
				$data .= 'NOTICE';
				break;
			default:
				$data .= 'DEBUG';
				break;
		}
		$data .= ']' . $msg . "\n\n";
		self::write(self::$_runtimeLogDir, 'rt', $data, $level);
	}

	static public function action($owner, $type, $extra = null){
		$data = time() . '|' . $owner . '|' . $type;
		if(isset($extra))
			$data .= '|' . $extra . "\n";

		self::write(self::$_actionLogDir, 'act', $data);
	}

	static private function seriousEnoughToLog($level){
		if($level <= self::$_runtimeLevel){
			return true;
		} else {
			return false;
		}
	}
	
	static private function write($dir, $prefix, $msg){
		$filename = $prefix . '_' . date('Ymd') . '.log';
		$fp = fopen($dir . '/' . $filename, 'a');
		if($fp){
			fwrite($fp, $msg);
			fclose($fp);
		}
	}
}
