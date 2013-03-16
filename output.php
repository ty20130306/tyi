<?php 

abstract class TyiOutputHandlerAbstract{
	public function __construct(){
		header('p3p:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
		header("Cache-Control: no-cache, must-revalidate, max-age=0");
	}

	public function setcookie($name, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false){
		setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
	}

	public function cleancookie($name, $path = '/', $domain = ''){
		setcookie($name, '', time() - 3600, $path, $domain);
	}

	abstract public function render(array $output);
}

class TyiOutputHandlerJson extends TyiOutputHandlerAbstract{
	public function render(array $output){
		header('Content-Type:text/plain;charset=utf-8');
		echo json_encode($output);
	}
}

class TyiOutputHandlerJsonp extends TyiOutputHandlerAbstract{
	private $_callback;
	public function __construct($callback){
		parent::__construct();
		$this->_callback = $callback;
	}
	
	public function render(array $output){
		header('Content-Type:text/plain;charset=utf-8');
		echo $this->_callback . '(' . json_encode($output) . ');';
	}
}

class TyiOutputHandlerXml extends TyiOutputHandlerAbstract{
	public function render(array $output){
		header('Content-Type:text/xml;charset=utf-8');
		echo '<?xml version="1.0" encoding="utf-8" ?><doc>', $this->_array2xml($output), "</doc>";
	}

	private function _array2xml(Array $array){
		$xml = '';
		foreach($array as $name => $value){
			if(!is_array($value)){
				$xml .= '<item key="' . htmlentities($name) . '">' . htmlentities($value) . '</item>';
			} else {
				$xml .= '<item key="' . htmlentities($name) . '">' . $this->_array2xml($value) . '</item>';
			}
		}
		return $xml;
	}
}

class TyiOutputHandlerHtml extends TyiOutputHandlerAbstract{
	private $_tpl = null;
	
	public function __construct($tpl){
		parent::__construct();
		$this->_tpl = $tpl;
	}
	
	public function render(array $output){
		header('Content-Type:text/html;charset=utf-8');

		$typeCfg = TysOutput::getTypeCfg(TysOutput::TYPE_HTML);
		$smarty = new Smarty ();
		$smarty->template_dir = $typeCfg['template_dir'];
		$smarty->compile_dir = $typeCfg['compile_dir'];
		$smarty->left_delimiter = '<!--{';
		$smarty->right_delimiter = '}-->';
		$smarty->caching = false;
		$smarty->assign('doc', $output);

		$smarty->display($this->_tpl);
	}
}

class TyiOutputHandlerAmf extends TyiOutputHandlerAbstract{
	public function render(array $output){
		header('Content-Type:application/x-amf');
		echo amf3_encode($output);
	}
}
