<?php

//Setup PHP
declare(ticks = 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors' , 1 );
ini_set('memory_limit', -1 );
set_time_limit( 0 );

require '../vendor/autoload.php';

if (class_exists('Dotenv')) {
    Dotenv::load(dirname(__DIR__));
}

//Boostrap Yammon
define( "APPLICATION_PATH" , realpath( dirname( __FILE__ ) .DIRECTORY_SEPARATOR . ".." ) .DIRECTORY_SEPARATOR );
define( 'YAMMON_NO_ERROR_HANDLING' , true );
require_once APPLICATION_PATH."yammon/yammon.php";

//Run Console
Console::run( );
