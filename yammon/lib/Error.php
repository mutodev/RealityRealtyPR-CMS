<?php

    if(!defined('E_STRICT'))            define('E_STRICT', 2048);
    if(!defined('E_RECOVERABLE_ERROR')) define('E_RECOVERABLE_ERROR', 4096);
    if(!defined('E_DEPRECATED'))        define('E_DEPRECATED', 8192);
    if(!defined('E_USER_DEPRECATED'))   define('E_USER_DEPRECATED', 16384 );
    if(!defined('E_EXCEPTION'))         define('E_EXCEPTION', -1 );

    class Error{

        protected static $logfp   = false;
        protected static $logging = false;
        protected static $started = false;
        protected static $errors  = array();
        protected static $fatal   = false;

        protected static $error_reporting = null;
        protected static $display_errors  = null;

		protected static $map = array(
			E_EXCEPTION => true ,
			E_ERROR => true ,
			E_PARSE => true ,
			E_CORE_ERROR => true ,
			E_COMPILE_ERROR => true ,
			E_USER_ERROR => true ,
			E_RECOVERABLE_ERROR => true ,
			E_WARNING => false ,
			E_NOTICE => false ,
			E_CORE_WARNING => false ,
			E_COMPILE_WARNING => false ,
			E_USER_WARNING => false ,
			E_USER_NOTICE => false ,
			E_STRICT => false ,
			E_DEPRECATED => false ,
			E_USER_DEPRECATED => false ,
		);


		protected static $debug_on_error_types   = 8191; //E_ALL | E_STRICT;
		protected static $debug_off_error_types  = 4437; //E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR;

        public static function log( $message ){

            if( !self::$logging )
                return;

            if( !self::$logfp ){
                @self::$logfp = fopen( Yammon::getWritablePath('logs') . 'error.log' , 'a' );
            }

            fwrite( self::$logfp , $message."\n" );
        }

        public static function start(){

            if( self::$started )
                return false;

            //Change And Save Error Configuration
            self::$error_reporting = error_reporting( E_ALL );
            self::$display_errors  = ini_set('display_errors' , '0' );

            //Register Callbacks
			$error_types = self::isDebugging() ? self::$debug_on_error_types : self::$debug_off_error_types;

            register_shutdown_function( array( "Error" , "onShutDown" ));
            set_error_handler( array( "Error" , "onError" ) ,  $error_types );
            set_exception_handler( array( "Error" , "onException" ));

            //Start Output Buffering
            ob_start();

            return self::$started = true;
        }

        public static function stop(){

            if( !self::$started )
                return false ;

            //Restore Configuration
            error_reporting( self::$error_reporting );
            ini_set('display_errors' , self::$display_errors );

            //Unregister callbacks
            restore_error_handler();
            restore_exception_handler();

            //Stop Output Buffering
            echo ob_get_clean();

            self::$started = false;

            return true;

        }


		public static function setDebugging( $debug ){
			Configure::write( 'debug' , $debug );
		}

		public static function isDebugging(){
			return Configure::read( 'debug' , false );
		}

        public static function onError( $errlevel , $message , $file , $line ){

            //Check if the error is fatal
            $is_fatal    = self::isErrorFatal( $errlevel );
            self::$fatal = self::$fatal || $is_fatal;

            //Check if it was supressed
            $was_suppresed = $is_fatal === false && (error_reporting() === 0);

            //Log the error
            $error = array(
                "level"     => $errlevel ,
                "message"   => $message ,
                "file"      => $file ,
                "line"      => $line ,
                "trace"     => self::isDebugging() ? self::getTrace() : null ,
                "supressed" => $was_suppresed ,
            );

            self::addError( $error );


            //Exit if it was a fatal error
            if( $is_fatal )
                exit();

            return true;

        }

        public static function onException( $exception ){

            $message = $exception->getMessage();
            $file    = $exception->getFile();
            $line    = $exception->getLine();

            //Set that it is fatal
            self::$fatal = true;

            //Log the error
            $error = array(
                "level"     => E_EXCEPTION ,
                "message"   => $exception->getMessage() ,
                "file"      => $exception->getFile() ,
                "line"      => $exception->getLine() ,
                "trace"     => self::isDebugging() ? $exception->getTrace() : null ,
                "supressed" => false
            );
            self::addError( $error );

        }

        public static function onShutDown(){

            //If we are not handling do nothing
            if( !self::$started )
               return;

            //Get the last error in case other
            //handlers coudn't get
            $error = error_get_last();
            if( $error !== null ){

                //Check if the error is fatal
                $is_fatal    = self::isErrorFatal( $error['type'] );
                self::$fatal = self::$fatal || $is_fatal;

                //Check if it was supressed
                $was_suppresed = $is_fatal === false && (error_reporting() === 0);

                $error = array(
                    "level"     => $error['type']    ,
                    "message"   => $error['message'] ,
                    "file"      => $error['file']    ,
                    "line"      => $error['line']    ,
                    "trace"     => self::isDebugging() ? self::getTrace() : null ,
                    "supressed" => $was_suppresed    ,
                    "shutdown"  => true ,
                );

                self::addError( $error );
            }

            //Log Errors
            self::logErrors( self::$errors );

            //Get the contents of the page
            $PAGE = ob_get_clean();

            //Send errors
            if( self::$fatal && !self::isDebugging() ){

                //Reroute error
                if( class_exists( 'Router' , false ) ){
                    try{
                        Router::fowardError( 500 );
                        return;
                    }catch( Exception $ex ){}
                }

                header('Internal Server Error' , TRUE , 500 );
                echo "INTERNAL SERVER ERROR 500";
                return;

            }

            //Display the page
            echo $PAGE;

        }

        public static function logErrors( $errors )
        {
            if ( self::isDebugging() )
                return;

            //Send Errors by email
            self::sendEmail( $errors );

            //Log error
            self::logInDatabase( $errors );
        }

        protected static function sendEmail( $errors )
        {
        }

        protected static function logInDatabase( $errors )
        {

            try {

                $doctrineConnection = Doctrine_Manager::connection();

                //No Connection
                if ( !$doctrineConnection || !$doctrineConnection->getDbh() )
                    return;

                $con = $doctrineConnection->getDbh();

                //Table Structure SQL
                $ym_error_table = "
                    CREATE TABLE IF NOT EXISTS `ym_error` (
                      `id` varchar(255) NOT NULL,
                      `type` varchar(255) NOT NULL DEFAULT 'PHP',
                      `level` varchar(255) NOT NULL,
                      `name` varchar(255) NOT NULL,
                      `message` varchar(1000) NOT NULL,
                      `file` varchar(500) NOT NULL,
                      `url` varchar(255) NOT NULL,
                      `line` int(10) unsigned NOT NULL,
                      `supressed` tinyint(1) unsigned NOT NULL,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                ";

                $ym_error_detail_table = "
                    CREATE TABLE IF NOT EXISTS `ym_error_detail` (
                      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                      `error_id` varchar(255) NOT NULL,
                      `date` datetime NOT NULL,
                      `user_id` bigint(20) unsigned DEFAULT NULL,
                      `data` text NOT NULL,
                      PRIMARY KEY (`id`),
                      KEY `error_id` (`error_id`),
                      CONSTRAINT `ym_error_detail_ibfk_1` FOREIGN KEY (`error_id`) REFERENCES `ym_error` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
                ";

                //Create Tables if not exists
                $con->query( $ym_error_table )->execute();
                $con->query( $ym_error_detail_table )->execute();

                //Prepare and Log Errors
                foreach( $errors as $key => $error ) {
                    $data = array();
                    $data['id']        = md5( $key );
                    $data['type']      = $error['type'] ? $error['type'] : 'PHP';
                    $data['level']     = $error['level'];
                    $data['message']   = $error['message'];
                    $data['name']      = $error['name'] ? $error['name'] : self::getErrorName( $error['level'] );
                    $data['file']      = $error['file'];
                    $data['line']      = $error['line'];
                    $data['url']       = $error['url'] ? $error['url'] : $_SERVER['REQUEST_URI'];
                    $data['supressed'] = $error['supressed'] ? 1 : 0;

                    //Log Error - Ignore Unique Error
                    try {
                        $sql = "INSERT INTO ym_error (id, type, level, name, message, file, url, line, supressed) VALUES (:id, :type, :level, :name, :message, :file, :url, :line, :supressed)";
                        $q = $con->prepare($sql);
                        $q->execute($data);
                    } catch( Exception $e ) { }

                    //Log Error Details
                    $data = array();
                    $data['error_id'] = md5( $key );
                    $data['date']     = date('Y-m-d H:i:s');
                    $data['user_id']  = Auth::isLoggedIn() ? Auth::getId() : null;

                    //Extra Data
                    $eData             = $error['data'] ? (array) $error['data'] : array();
                    $eData['_GET']     = $_GET;
                    $eData['_POST']    = $_POST;
                    $eData['_SESSION'] = $_SESSION;
                    $eData['_COOKIE']  = $_COOKIE;
                    $eData['_ENV']     = $_ENV;
                    $eData['_SERVER']  = $_SERVER;
                    $eData['_FILES']   = $_FILES;

                    $data['data'] = print_r($eData, true);

                    $sql = "INSERT INTO ym_error_detail (error_id, date, user_id, data) VALUES (:error_id, :date, :user_id, :data)";
                    $q = $con->prepare($sql);
                    $q->execute($data);
                }

            } catch( Exception $e ) {
                return;
            }

        }

        protected static function addError( $error ){

            $message = self::getErrorName( $error['level'] ).": ".$error['message']." ".$error['file'].":".$error['line'];
            if( !isset( self::$errors[ $message ] ) ){

                //Lof the error
                self::log( $message );

                //Display the error
                self::display( $error );

            }

            //Store the error
            self::$errors[ $message ] = $error;

        }

        protected static function display( $error ){

            //If we are not debugging dont show errors
            if( !self::isDebugging() )
                return;

            //Check if the error was suprresed
            if( $error['supressed'] )
                return false;

            //Check if we show errors of that level
            $level = $error['level'];
            if( $level !== E_EXCEPTION ){
                if( !(self::$error_reporting & $level) ){
                    return;
                }
            }

            //Check if we are displaying errors
            if( empty( self::$display_errors ) )
                return false;


            //Show the error
            $error_name = self::getErrorName( $error['level'] );

            $html = array();
            $html[] = "<table style='margin:10px 0;' cellpadding='0' cellspacing='0'>";
                $html[] = "<thead>";
                    $html[] = "<tr>";
                        $html[] = "<th colspan='3' style='background:#F57900;font-weight:bold;text-align:left;padding:5px'>";
                            $html[] = $error_name.": ";
                            $html[] = $error['message'];
                            $html[] = " in ";
                            $html[] = $error['file'];
                            $html[] = ":";
                            $html[] = $error['line'];
                        $html[] = "</th>";
                    $html[] = "</tr>";
                $html[] = "</thead>";
                $html[] = "<tbody>";
                        $html[] = "<tr>";
                            $html[] = "<th colspan='3' style='background:#DFB472;font-weight:bold;text-align:left;padding:5px'>";
                                $html[] = "Call Stack";
                            $html[] = "</th>";
                        $html[] = "</tr>";
                        $html[] = "<tr style='background:#CFCFCF'>";
                            $html[] = "<th style='border:1px solid white;font-weight:bold;text-align:left;padding:3px'>";
                                $html[] = "#";
                            $html[] = "</th>";
                            $html[] = "<th style='border:1px solid white;font-weight:bold;text-align:left;padding:3px'>";
                                $html[] = "Function";
                            $html[] = "</th>";
                            $html[] = "<th style='border:1px solid white;font-weight:bold;text-align:left;padding:3px'>";
                                $html[] = "Location";
                            $html[] = "</th>";
                        $html[] = "</tr>";
                    if( empty( $error['trace'] ) ){
                          $html[] = "<tr style='background:#EEEEEC;'>";
                                $html[] = "<td style='border:1px solid white;padding:20px;text-align:center' colspan='3'>";
                                    $html[] = "NO TRACE ( possibly a parse error)";
                                $html[] = "</td>";
                            $html[] = "</tr>";
                    }else{
                        foreach( $error['trace'] as $i => $trace ){
                            $html[] = "<tr style='background:#EEEEEC;'>";
                                $html[] = "<td style='border:1px solid white;padding:3px'>";
                                    $html[] = $i;
                                $html[] = "</td>";
                                $html[] = "<td style='border:1px solid white;padding:3px'>";
                                    if( isset( $trace['class'] ))     $html[] = $trace['class'];
                                    if( isset( $trace['type'] ))      $html[] = $trace['type'];
                                    elseif( isset( $trace['class'] )) $html[] = "::";
                                    if( isset( $trace['function'] ))  $html[] = $trace['function'];
                                $html[] = "</td>";
                                $html[] = "<td style='border:1px solid white;padding:3px'>";
                                    if( isset( $trace['file'] ))  $html[] = $trace['file'];
                                    if( isset( $trace['line'] ))  $html[] = ":".$trace['line'];
                                $html[] = "</td>";
                            $html[] = "</tr>";
                        }
                    }
                $html[] = "</tbody>";
            $html[] = "</table>";

            echo implode( "" , $html );

        }

        protected static function isErrorFatal( $level ){

			if( isset( self::$map[ $level ] ))
				return self::$map[ $level ];

            return false;

        }

        protected static function getErrorName( $level ){

            switch( $level ){
                case E_WARNING:
                case E_USER_WARNING:
                case E_CORE_WARNING:
                case E_COMPILE_WARNING:
                case E_RECOVERABLE_ERROR:
                                          return "Warning";
                                          break;


                case E_STRICT:            return "Strict";
                                          break;

                case E_DEPRECATED:        return "Deprecated";
                                          break;

                case E_NOTICE:
                case E_USER_NOTICE:
                                          return "Notice";
                                          break;

                case E_EXCEPTION:
                                          return "Exception";

                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                                          return "Fatal Error";
                                          break;

                case E_RECOVERABLE_ERROR:
                                          return "Recoverable Error";
                                          break;

                default:
                                          return "Unknown Error";
                                          break;
            }

        }

        protected static function getTrace(){

             if( !self::isDebugging() ){
                return array();
             }

             //Prevent memory problems
             $memory_limit          = ini_set('memory_limit' , -1 );
             $xdebug_collect_params = ini_set('xdebug.collect_params' , 0 );

             if( function_exists( 'xdebug_get_function_stack' ) ){
                $trace = xdebug_get_function_stack();
                $trace = array_reverse( $trace );
                array_shift( $trace );
                array_shift( $trace );
             }else{
                $trace = debug_backtrace( false );
                array_shift( $trace );
                array_shift( $trace );
             }

             //Clean the trance of objects and args to save memory
             foreach( $trace as &$t ){
                unset( $t['object'] );
                unset( $t['params'] );
             }

             //Restore Memory Limit
             ini_set('memory_limit'          , $memory_limit );
             ini_set('xdebug.collect_params' , $xdebug_collect_params );

             return $trace;

        }


    }
