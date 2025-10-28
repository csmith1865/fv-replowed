<?php
$config = new Amfphp_Core_Config();
$config->serviceFolderPaths = array(
    dirname(__FILE__) . "/Functions/"
);

// Debug
error_log(
    "Config loaded - " . date('c') . "\n" . 
    "Service paths: " . print_r($config->serviceFolderPaths, true) . "\n",
    3,
    dirname(__FILE__) . "/config_debug.log"
);