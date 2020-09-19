<?php

 
$config = array('debug' => true , 'app_dir'=> "../", 'error_page'=>'error500.html' );


// Un fichero log por mes
ini_set('error_log', $_SERVER['DOCUMENT_ROOT'].'/TutoZ/logs/php_log_' .date('Y-m') .'.log');

//echo ini_get('error_log');

//error_reporting(0);
/**
* Error handler, passes flow over the exception logger with new ErrorException.
*/
function log_error( $num, $str, $file, $line, $context = null )
{   
    log_exception( new ErrorException( $str, 0, $num, $file, $line ) );
}

/**
* Uncaught exception handler.
*/
function log_exception( Object $e )
{
    
    if ($e instanceof Error) 
    {
        $e = new ErrorException($e);
    }

    global $config;
    if ( $config["debug"] == true )
    {
        print "<div style='text-align: center;'>";
        print "<h2 style='color: rgb(190, 50, 50);'>Exception Occured:</h2>";
        print "<table style='width: 800px; display: inline-block;'>";
        print "<tr style='background-color:rgb(230,230,230);'><th style='width: 80px;'>Type</th><td>" . get_class( $e ) . "</td></tr>";
        print "<tr style='background-color:rgb(240,240,240);'><th>Message</th><td>{$e->getMessage()}</td></tr>";
        print "<tr style='background-color:rgb(230,230,230);'><th>File</th><td>{$e->getFile()}</td></tr>";
        print "<tr style='background-color:rgb(240,240,240);'><th>Line</th><td>{$e->getLine()}</td></tr>";
        print "</table></div>";
        $message = "Type: " . get_class( $e ) . "; Message: {$e->getMessage()}; File: {$e->getFile()}; Line: {$e->getLine()};";
        //$ok = file_put_contents( $config["app_dir"] . "borrar_habitual.log", $message . PHP_EOL, FILE_APPEND );
        //echo "bien:".$ok;
        error_log($message);
    }
    else
    {
        $message ="Type: " . get_class( $e ) . "; Message: {$e->getMessage()}; File: {$e->getFile()}; line: {$e->getLine()};";
        //$ok = file_put_contents( $config["app_dir"] . "borrar_habitual.log", $message . PHP_EOL, FILE_APPEND );
        error_log($message);
        //header( "Location: {$config["error_page"]}" );
    }
   
    exit();
}

/**
* Checks for a fatal error, work around for set_error_handler not working on fatal errors.
*/
function check_for_fatal()
{
    $error = error_get_last();
    if ( $error["type"] == E_ERROR )
        log_error( $error["type"], $error["message"], $error["file"], $error["line"] );
}

register_shutdown_function( "check_for_fatal" );
set_error_handler( "log_error" );
set_exception_handler( "log_exception" );
ini_set( "display_errors", "off" );
error_reporting( E_ALL );

    
function mi_info_log($message)
{
   error_log("INFO: ".var_export($message,true));
}

?>