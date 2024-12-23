<?php

// prevent direct access
if ( !defined( 'ABSPATH' ) ) exit;

/**
* PSR-4 namespace autoloader
* https://gist.github.com/Shelob9/acf3109c006f2957c12ea6b317f549f2
*/

spl_autoload_register(function($class) {

    $prefix = 'Eva\\Plugin\\';
    $baseDir = DPI_EVA_FEED_DIR . '/Plugin/';
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) return;

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }

});