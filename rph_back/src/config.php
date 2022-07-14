<?php

if ( ! is_file(ROOT_PATH . '/config/config.inc')) {
    exit('Not configured.');
}

require_once 'config/config.inc';

// Set up configuration from main_config array
Config::update($main_config);
