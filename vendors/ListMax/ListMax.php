<?php
	
	$zend_path = VENDORS_PATH . "Zend" . DS;
    set_include_path( $zend_path . PATH_SEPARATOR . DOCUMENT_ROOT . PATH_SEPARATOR . get_include_path()  );
    require_once('seo.php');

    //Include Dependencies
	require_once('Zend/Db.php');
	include_once('Zend/Cache.php');
	include_once('Zend/Translate.php');
	include_once('Zend/Mail.php');
		
    //Calculate Paths		
	define( 'LISTMAX_API_PATH'        , dirname(realpath(__FILE__))  . DIRECTORY_SEPARATOR );
	define( 'LISTMAX_LOCALE_PATH'     , LISTMAX_API_PATH . "locale"  . DIRECTORY_SEPARATOR );
	
	//Register Autoload
    function listmax_autoload( $class_name ){
	
        //Replace the class name		
        $split  = explode( "_" , $class_name );
        $first  = array_shift( $split );
                
        //Only load listmax classes
        if( $first != "ListMax" )
            return false;
       	           	    
        //Find out the filename
        $path = LISTMAX_API_PATH;
        $filename = $path.implode( DIRECTORY_SEPARATOR , $split ).".php";
	    
        if( file_exists( $filename ) && is_readable( $filename ) ){
            require_once $filename;
            return true;
        }
        
        return false;
			 
    }
    spl_autoload_register( 'listmax_autoload' );    

    //Define Listmax Class and instance
    require_once( LISTMAX_API_PATH."Core.php" );
    class ListMax extends ListMax_Core{
    }
	global $ListMax;
    $ListMax = ListMax::singleton();
    
	
