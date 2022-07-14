<?php

// Autoloader for classes from src/lib/

spl_autoload_register (function ($class) {
    $class = str_replace("\\", DIRECTORY_SEPARATOR, $class) . '.php';
    if (!is_file (LIB_PATH .  $class)) {
        throw new \LogicException ("Class $class not found");
    }
    require (LIB_PATH . $class);
});

?>