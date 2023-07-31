<?php
	
    class ListMax_Core{
    
        protected static $database  = false;
        protected static $caches    = array();
        protected static $translate = false;
                
        protected static $instance;
        protected static $config = array(
            "database_driver"         => "mysqli"     ,
            "database_host"           => "localhost"  ,
            "database_name"           => "cmsoldrr"      ,
            "database_username"       => "root"    ,
            "database_password"       => "root"     ,
            "cache_on"                => true         ,
            "cache_lifetime"          => 1200         ,
            "cache_counters_lifetime" => 86400        ,
            "translation_on"          => false        ,
            "translation_lang"        => "es"         , 
            "translation_country"     => "PR"         ,
            "mail_from"               => ""           ,
            "mail_from_name"          => ""           ,
            "mail_bcc"                => ""           ,
            "mail_return_path"        => ""           ,
            "mail_subject"            => ""           ,
            "master"                  => true        ,
        );
        
        /* -- Singleton Methods ------------------ */        
        protected function __construct(){
        
        }
        /* --------------------------------------- */
        public static function singleton(){
            if (!isset(self::$instance)) {
                $c = __CLASS__;
                self::$instance = new $c;
            }
            return self::$instance;
        }
        /* --------------------------------------- */        
        public function __clone(){
            trigger_error('Clone is not allowed.', E_USER_ERROR);
        }
        
        /* -- Configuration Methods -------------- */        
        public static function config( $key , $value = null ){
            
            //Normalize the key
            $key = trim( strtolower( $key ) );
            
            //Check if the config exists
            $exists = isset( ListMax::$config[ $key ] );
            
            //Dont do anything
            if( !$exists ){
                return null;
            }
            
            //Set Or get the value
            if( $value !== null && $exists ){
                ListMax::$config[ $key ] = $value;
            }else{ //Get the value
                $value = ListMax::$config[ $key ];
            }
        
            //Return the value
            return $value;
        }
        
        /* -- Translation Methods ---------------- */        
        public static function t(){

            $args      = func_get_args();
            $string    = array_shift( $args );
            $translate = ListMax::translate();

            //Translate the string
            if( $translate ){
                $string = $translate->translate( $string );
            }

	        //Do Replacements
		    for( $x =1 ; $x <= count($args) ; $x++ ){
	    		$string = str_replace("%$x" , $args[$x-1] , $string );
		    }

	        //Return
            return $string;
		    
        }
        /* -- Acessor Methods -------------------- */        
		public function __call( $key , $arguments ){
           
            if( strlen( $key ) > 3 && substr( $key , 0 , 3 ) == "get"){
                $module = substr( $key , 3  );
                $obj = $this->get( $module );
                return $obj;
            }
            
			trigger_error( "called not existing method $key"  , E_USER_ERROR );
			
		}	
        /* ------------------------------------------- */
        public function get( $module ){        
            $module = ucwords( $module );
            $class  = "ListMax_Module_".$module."_Collection";                
            return new $class();    
        }        
        
        /* -- Global Helper Methods -------------- */        
        public static function db(){
        
            if( ListMax::$database !== false ){            
                return ListMax::$database;
            }
          
        	$db = Zend_Db::factory( Listmax::config( "database_driver" ) , 
        	                        array( 
                                          'host'     => Listmax::config( "database_host" )     , 
                                          'dbname'   => Listmax::config( "database_name" )     , 
                                          'username' => Listmax::config( "database_username" ) , 
                                          'password' => Listmax::config( "database_password" ) 
            ));

            //Set Utf-8 on the database
        	$db->query("SET NAMES utf8 COLLATE utf8_unicode_ci;");
            
            ListMax::$database = $db;
            return $db;
            
        }        
        /* --------------------------------------- */    
        public static function cache( $name = '' ){

            if( !Listmax::config( "cache_on" ) )
                return null;
                
            if( $name === null )
                return null;
                
            if( empty( $name ) ){
                $name = "default";
            }
                        
            $name = trim( strtolower( $name ) );

            if( isset( ListMax::$caches[ $name ] ) !== false ){            
                return ListMax::$caches[ $name ];
            }
        
            //Check that we have memcache
            if( !class_exists('Memcache') )
                return null;
                            
            //Get the cache lifetime
            $cache_lifetime = Listmax::config( "cache_{$name}_lifetime" );                        
            if( empty($cache_lifetime) )
                $cache_lifetime = Listmax::config( "cache_lifetime" );
                
            //Check that we have a cache lifetime                
            if( empty( $cache_lifetime ) )
                return null;
                        
            //Create Cache        
            $cacheFrontendOptions = array(
               'lifetime'                  => $cache_lifetime ,
               'automatic_serialization'   => true ,
            );

            $cacheBackend = "Memcached";    
            $cacheBackendOptions = 	array(
                    array(
                    'host' => 'localhost', 
                    'port' => 11211, 
                    'persistent' => true, 
                    'weight' => 1, 
                    'timeout' => 5, 
                    'retry_interval' => 15, 
                    'status' => true, 
                    )
            );

            try{
                $cache = Zend_Cache::factory( 'Core', $cacheBackend , $cacheFrontendOptions, $cacheBackendOptions );
            }catch( Exception $ex ){
                return null;
            }

            ListMax::$caches[ $name ] = $cache;
            return $cache;
     
        }
        /* --------------------------------------- */    
        public static function translate(){

            if( ListMax::$translate !== false ){            
                return ListMax::$translate;
            }

            if( !Listmax::config( "translation_on" ) )
                return null;

            $LANG     = strtolower( ListMax::config("translation_lang") );
            $CC       = strtoupper( ListMax::config("translation_country") );
            $filename = LISTMAX_LOCALE_PATH.$LANG.$CC.".mo";
            $cache    = ListMax::cache("translation");
            
            try{
                $translate  = new Zend_Translate( 'gettext', $filename , $LANG , array() );
            }catch( Exception $ex ){
                $translate = null;
            }

            ListMax::$translate = $translate;
            return $translate;
     
        }
        /* --------------------------------------- */        
        public function query( $select ,  $namespace = "collection" , $cachename = "" ){

            $db    = ListMax::db();
            $cache = ListMax::cache( $cachename  );
                                              
            if( !is_string($select) )
                $select = $select->__toString();
                
            $select = '/*'.(@$_SERVER['HTTP_HOST']).'*/'.$select;
    
            if( !$cache ){
               $result = $db->fetchAll( $select );
            }else{

                $key = "lmapi_".md5( $select );
                if( !$result = $cache->load($key) ) {
                    $result = $db->fetchAll( $select );
                    $cache->save( $result , $key , array($namespace) );   
                }
                
            }

            return $result;

        }              
        /* --------------------------------------- */        

    }
    
