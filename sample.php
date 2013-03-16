<?php

require_once 'tyi.php';
require_once 'Smarty.class.php';

// step 1. configure your output type of html
$htmlOutputCfg = array(
	'template_dir'	=> your/dir/to/template,
	'compile_dir'	=> your/dir/to/compile
);
TysOutput::initTypeCfg(TysOutput::TYPE_HTML, $htmlOutputCfg);

// step 2. configure your log dir 
TyiHelperLog::init(your/dir/to/log/runtime, your/dir/to/log/action);

// step 3. run it
Tyi::run(your/dir/to/tys);
