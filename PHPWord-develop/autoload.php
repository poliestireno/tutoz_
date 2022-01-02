
<?php
$GLOBALS['class_path'] = array(__DIR__ . '/lib','/PhpWord',__DIR__ .'/PhpWord', __DIR__);

// Set-up class_path superglobal variable using php include_path as basis
if (!array_key_exists('class_path', $GLOBALS)) {
    $GLOBALS['class_path'] = array();
    foreach (explode(PATH_SEPARATOR, get_include_path()) as $path) {
        // substitute __DIR__ path for '.' instead
        if ($path == '.') {
            array_push( $GLOBALS['class_path'], realpath(__DIR__) );
            continue;
        }
        array_push( $GLOBALS['class_path'], realpath($path) );
    }
}

if (!function_exists('import')):
function import($package = '') {
    if (empty($package)) {
        trigger_error("Package path must be specified.", E_USER_ERROR);
    }
    $package_bits = explode('\\', $package);
    $package_path = implode(DIRECTORY_SEPARATOR, $package_bits) . '.php';
    foreach ($GLOBALS['class_path'] as $path) {
        $file = $path . DIRECTORY_SEPARATOR . $package_path;
        //echo 'afile:'.$file;
        if (file_exists($file)) {
            require_once($file);
            $entity_name = implode('\\', $package_bits);
            if (!(class_exists($entity_name, false) ||
                interface_exists($entity_name, false)
                || trait_exists($entity_name, false))) {
            $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
            trigger_error("Entity '" . $package . "' not found in file '" . $package_path . "' for import called in " .
                    $caller['file'] . " on line " . $caller['line'], E_USER_ERROR);
            }
            return;
        }
    }
}
endif;

spl_autoload_register('import');
?>