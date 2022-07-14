<?php

define('ROOT_PATH', realpath($_SERVER["DOCUMENT_ROOT"] . '/..') . '/');
define('SRC_PATH', ROOT_PATH . 'src/');
define('LIB_PATH', SRC_PATH . 'lib/');

$include_path = ROOT_PATH
    . PATH_SEPARATOR . SRC_PATH;

set_include_path($include_path);

require_once 'main.php';

?>
