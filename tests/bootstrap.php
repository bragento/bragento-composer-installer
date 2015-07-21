<?php
define('DS', DIRECTORY_SEPARATOR);
define('APPLICATION_BASE_DIR', dirname(__DIR__));
define('TEST_RES_BASE_DIR', APPLICATION_BASE_DIR . DS . 'res' . DS . 'tests');
define('TEST_BUILD_DIR', APPLICATION_BASE_DIR . DS . 'build' . DS . 'tests');
define('VENDOR_DIR', APPLICATION_BASE_DIR . DS . 'vendor');
define('COMPOSER_AUTOLOADER', VENDOR_DIR . DS . 'autoload.php');

require_once COMPOSER_AUTOLOADER;
