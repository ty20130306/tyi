<?php
require_once __DIR__ . '/input.php';
require_once __DIR__ . '/output.php';
require_once __DIR__ . '/tys.php';
require_once __DIR__ . '/helper/log.php';

class TyiException extends Exception{
	
}

class Tyi{
	private static $_inputHandler = null;
	private static $_outputHandler = null;
	
	public static function input($name, $default = null){
		return self::$_inputHandler->get($name, $default);
	}
	
	public static function run($tysDir){
		// step 1. input
		switch($_SERVER['CONTENT_TYPE']){
			case 'application/x-amf':
				self::$_inputHandler = new TyiInputHandlerAmf();
				break;
			default:
				self::$_inputHandler = new TyiInputHandlerHttp();
				break;
		}
		
		// step 2. locate tys
		$uri = preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']);
		$classPath = preg_replace('/.tyi$/', '.php', $uri);
		
		$lineToUpper = preg_replace('/_([a-z])/ie', "strtoupper('\\1')", str_replace('.tyi', '', $uri));
		$dirToLineAndUpper = preg_replace('/\/([a-z])/e', "'_'.strtoupper('\\1')", $lineToUpper);
		$className = 'Tys' . $dirToLineAndUpper;
		
		if(!file_exists($tysDir . '/' . $classPath)){
			throw new TyiException('interface file missing,file=' . $tysDir . '/' . $classPath);
		}
		
		// step 3. run tys
		require_once $tysDir . '/' . $classPath;
		$tys = new $className();
		
		try {
			$tys->run();
			$tysOutput = $tys->getTysOutput();
		} catch (TysException $e){
			$tysOutput = $e->getTysOutput();
			Tyi_Helper_Log::runtime($e->getMessage(), Tyi_Helper_Log::RUNTIME_LEVEL_ERROR);
		}
		
		// step 4. output
		switch (TysOutput::getType()){
			case TysOutput::TYPE_XML:
				self::$_outputHandler = new TyiOutputHandlerXml();
				break;
			case TysOutput::TYPE_HTML:
				$tpl = preg_replace('/^\//i', '', str_replace('.tyi', '.html', $uri));
				self::$_outputHandler = new TyiOutputHandlerHtml($tpl);
				break;
			case TysOutput::TYPE_AMF:
				self::$_outputHandler = new TyiOutputHandlerAmf();
				break;
			case TysOutput::TYPE_JSONP:
				$callback = self::$_inputHandler->get('callback');
				self::$_outputHandler = new TyiOutputHandlerJsonp($callback);
				break;
			default:
				self::$_outputHandler = new TyiOutputHandlerJson();
				break;
		}

		self::$_outputHandler->render($tysOutput->getOutput());
	}
}
