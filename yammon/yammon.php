<?php

    define( "DS"            , DIRECTORY_SEPARATOR );
	define( "YAMMON_PATH"   , realpath(dirname( __FILE__ )).DS );
	define( "DOCUMENT_ROOT" , realpath($_SERVER['DOCUMENT_ROOT']).DS );

	if( !defined('YAMMON_LEAN' ) )              define( 'YAMMON_LEAN'              , 0 );
	if( !defined('YAMMON_NO_ERROR_HANDLING' ) ) define( 'YAMMON_NO_ERROR_HANDLING' , 0 );
	if( !defined('YAMMON_NO_SESSION' ) )        define( 'YAMMON_NO_SESSION'        , 0 );
	if( !defined('YAMMON_NO_DB' ) )             define( 'YAMMON_NO_DB'             , 0 );
	if( !defined('YAMMON_NO_BOOTSTRAP' ) )      define( 'YAMMON_NO_BOOTSTRAP'      , 0 );

    //Load Yammon Class
    require_once YAMMON_PATH."/lib/Yammon.php";

    //Register Autloads
	Yammon::setYammonPath( YAMMON_PATH );
	Yammon::setApplicationPath( APPLICATION_PATH );
	Yammon::registerAutoload();

	//Load Config
	Yammon::loadConfig();

	//Load Functions
	Yammon::loadFunctions();

	//Start Error Handling
    /*
    if( !YAMMON_LEAN && !YAMMON_NO_ERROR_HANDLING ){
	  Error::start();
    }
    */

Error::start();

    //Setup Doctrine
    if( !YAMMON_LEAN && !YAMMON_NO_DB ){
        require_once( YAMMON_PATH.DS."doctrine.php" );
    }

    //Set Time Zone
    if( Configure::read('timezone') )
        ini_set( 'date.timezone' , Configure::read('timezone') );

    //Load User defined bootstrap
    if( !YAMMON_LEAN && !YAMMON_NO_BOOTSTRAP ){
        Yammon::loadBoostraps();
    }
