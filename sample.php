<?php

require_once 'tyi.php';
require_once 'Smarty.class.php';

// step 1. configure your smarty
Tyi::initSmartyCfg(your/dir/to/template, your/dir/to/compile);

// step 2. configure your log dir 
Tyi::initLog(your/dir/to/log/runtime, your/dir/to/log/action);

// step 3. run it
Tyi::run(your/dir/to/tys);
