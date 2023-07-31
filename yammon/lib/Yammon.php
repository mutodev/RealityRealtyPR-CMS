<?php

    class Yammon{

        const VERSION               = '1.1';

        const DIRNAME_ACTIONS       = 'actions';
        const DIRNAME_CACHE         = 'cache';
        const DIRNAME_CONFIG        = 'config';
        const DIRNAME_CRONS         = 'crons';
        const DIRNAME_FUNCTIONS     = 'functions';
        const DIRNAME_LAYOUTS       = 'layout';
        const DIRNAME_LIBRARY       = 'lib';
        const DIRNAME_VENDORS       = 'vendors';
        const DIRNAME_MODELS        = 'models';
        const DIRNAME_TMP           = 'tmp';
        const DIRNAME_WRITABLE      = 'writable';
        const DIRNAME_VIEW          = 'views';

        const PATH_TYPE_ACTIONS     = 'action';
        const PATH_TYPE_CONFIG      = 'config';
        const PATH_TYPE_CRONS       = 'cron';
        const PATH_TYPE_FUNCTIONS   = 'function';
        const PATH_TYPE_LAYOUTS     = 'layout';
        const PATH_TYPE_LIBRARY     = 'library';
        const PATH_TYPE_VENDORS     = 'vendor';
        const PATH_TYPE_MODELS      = 'model';
        const PATH_TYPE_VIEWS       = 'view';

        static private $paths            = array();
        static private $application_path = null;
        static private $yammon_path      = null;

        public static function addPath( $path , $type = self::PATH_TYPE_LIBRARY ){

            if( substr( $path , -1 , 1 ) != '/' )
                $path .= '/';

            if( !is_dir( $path ) )
                return;

            $path = realpath( $path ).DS;

            if( !isset( self::$paths[ $type ] ) )
                self::$paths[ $type ] = array();

            if( !in_array( $path , self::$paths[ $type ] ))
                array_unshift( self::$paths[ $type ] , $path );

        }

        public static function removePath( $path , $type = self::PATH_TYPE_LIBRARY )
        {

            if( substr( $path , -1 , 1 ) != '/' )
                $path .= '/';

            if( !isset( self::$paths[ $type ] ) )
                return;

            $path = realpath( $path ).DS;

            foreach( self::$paths[ $type ] as $k => $v ){
                if( $v == $path ){
                    unset( self::$paths[ $type ][ $k ] );
                }
            }

        }

        public static function setApplicationPath( $path ){

            if( substr( $path , -1 , 1 ) != '/' )
                $path .= '/';

            self::$application_path = $path;
            self::addActionsPath( $path . self::DIRNAME_ACTIONS );
            self::addConfigPath( $path . self::DIRNAME_CONFIG );
            self::addCronsPath( $path . self::DIRNAME_CRONS );
            self::addFunctionsPath( $path .self::DIRNAME_FUNCTIONS );
            self::addLayoutPath( $path . self::DIRNAME_LAYOUTS );
            self::addLibraryPath( $path . self::DIRNAME_LIBRARY );
            self::addVendorsPath( $path . self::DIRNAME_VENDORS );
            self::addModelsPath( $path . self::DIRNAME_MODELS );
            self::addViewsPath( $path . self::DIRNAME_VIEW );
        }

        public static function setYammonPath( $path ){

            if( substr( $path , -1 , 1 ) != '/' )
                $path .= '/';

            self::$yammon_path = $path;
            self::addActionsPath( $path . self::DIRNAME_ACTIONS );
            self::addConfigPath( $path . self::DIRNAME_CONFIG );
            self::addCronsPath( $path . self::DIRNAME_CRONS );
            self::addFunctionsPath( $path .self::DIRNAME_FUNCTIONS );
            self::addLayoutPath( $path . self::DIRNAME_LAYOUTS );
            self::addLibraryPath( $path . self::DIRNAME_LIBRARY );
            self::addVendorsPath( $path . self::DIRNAME_VENDORS );
            self::addModelsPath( $path . self::DIRNAME_MODELS );
            self::addViewsPath( $path . self::DIRNAME_VIEW );

        }

        public static function addActionsPath( $path ){
            return self::addPath( $path , self::PATH_TYPE_ACTIONS );
        }

        public static function removeActionsPath( $path ){
            return self::removePath( $path , self::PATH_TYPE_ACTIONS );
        }

        public static function addConfigPath( $path ){
            return self::addPath( $path , self::PATH_TYPE_CONFIG );
        }

        public static function removeConfigPath( $path ){
            return self::removePath( $path , self::PATH_TYPE_CONFIG );
        }

        public static function addCronsPath( $path ){
            return self::addPath( $path , self::PATH_TYPE_CRONS );
        }

        public static function removeCronsPath( $path ){
            return self::removePath( $path , self::PATH_TYPE_CRONS );
        }

        public static function addFunctionsPath( $path ){
            return self::addPath( $path , self::PATH_TYPE_FUNCTIONS );
        }

        public static function removeFunctionsPath( $path ){
            return self::removePath( $path , self::PATH_TYPE_FUNCTIONS );
        }

        public static function addLayoutPath( $path ){
            return self::addPath( $path , self::PATH_TYPE_LAYOUTS );
        }

        public static function removeLayoutPath( $path ){
            return self::removePath( $path , self::PATH_TYPE_LAYOUTS );
        }

        public static function addLibraryPath( $path ){
            return self::addPath( $path , self::PATH_TYPE_LIBRARY );
        }

        public static function removeLibraryPath( $path ){
            return self::removePath( $path , self::PATH_TYPE_LIBRARY );
        }

        public static function addModelsPath( $path ){
            return self::addPath( $path , self::PATH_TYPE_MODELS );
        }

        public static function removeModelsPath( $path ){
            return self::removePath( $path , self::PATH_TYPE_MODELS );
        }

        public static function addVendorsPath( $path ){
            return self::addPath( $path , self::PATH_TYPE_VENDORS );
        }

        public static function removeVendorsPath( $path ){
            return self::removePath( $path , self::PATH_TYPE_VENDORS );
        }

        public static function addViewsPath( $path ){
            return self::addPath( $path , self::PATH_TYPE_VIEWS );
        }

        public static function removeViewsPath( $path ){
            return self::removePath( $path , self::PATH_TYPE_VIEWS );
        }

        public static function getPaths( $type = self::PATH_TYPE_LIBRARY ){
            if( isset( self::$paths[ $type ] ) )
                return self::$paths[ $type ];
            else
                return array();
        }

        public static function getApplicationPath(){
            return self::$application_path;
        }

        public static function getYammonPath(){
            return self::yammon_path;
        }

        public static function getActionsPaths(  ){
            return self::getPaths( self::PATH_TYPE_ACTIONS );
        }

        public static function getCachePath( $path = null ){
            return self::getWritablePath( self::DIRNAME_CACHE . DS . $path );
        }

        public static function getConfigPaths(  ){
            return self::getPaths( self::PATH_TYPE_CONFIG );
        }

        public static function getCronsPaths(  ){
            return self::getPaths( self::PATH_TYPE_CRONS  );
        }

        public static function getFunctionsPaths(){
            return self::getPaths( self::PATH_TYPE_FUNCTIONS );
        }

        public static function getLayoutsPaths( ){
            return self::getPaths( self::PATH_TYPE_LAYOUTS );
        }

        public static function getLibrariesPaths( ){
            return self::getPaths( self::PATH_TYPE_LIBRARY );
        }

        public static function getModelsPaths(  ){
            return self::getPaths( self::PATH_TYPE_MODELS );
        }

        public static function getTemporaryPath( $path = null ){
            return self::getWritablePath( self::DIRNAME_TMP . DS . $path );
        }

        public static function getTemporaryFile( $path = null ){
            $dir = self::getWritablePath( self::DIRNAME_TMP . DS . $path );
            return tempnam( $dir , 'tmp' );
        }

        public static function getWritablePath( $path = null ){

            $writable = self::$application_path . self::DIRNAME_WRITABLE . DS;

            if( $path )
                $path = $writable . $path;
            else
                $path = $writable;

            if( substr( $path , -1 , 1 ) != '/' )
                $path .= '/';

            if( !file_exists( $path ) )
                FS::makeDirectory( $path );

            return $path;

        }

        public static function getVendorsPaths( ){
            return self::getPaths(self::PATH_TYPE_VENDORS );
        }

        public static function getViewsPaths( ){
            return self::getPaths( self::PATH_TYPE_VIEWS );
        }

        private static function autoload( $class_name ){
            self::load( $class_name );
        }

        public static function registerAutoload(){
            spl_autoload_register( array( __CLASS__ , 'autoload'));
        }

        public static function load( $class_name ){

            //Replace the class name
            $class_name = str_replace( '_', '/', $class_name );

            //Look in the paths for the class
            $paths = self::getLibrariesPaths();
            foreach( $paths as $path ){

                //Construct the full path
                $path .= $class_name.'.php';

                //If it exists load it
                if( file_exists( $path ) && is_readable( $path) ){
                    require_once $path;
                    return true;
                }

            }

            return false;
        }

        public static function findSubClasses( $classname , $include_abstract = false ){

            $paths           = self::getLibrariesPaths();
            $sub_path        = str_replace( '_', '/', $classname );
            $subclasses      = array();

            foreach( $paths as $path ){
                $full_path = $path.$sub_path;
                $files     = FS::findFiles('*.php' , true  , $full_path );
                foreach( $files as $file ){

                    //Get the class name out of the file
                    $subclass = dirname( $file ).DS.basename( $file , ".php" );
                    $subclass = substr( $subclass , strlen( $path ) );
                    $subclass = str_replace( '/', '_', $subclass );

                    //Make sure that the class exists
                    if( !class_exists( $subclass ) )
                        continue;

                    //Make sure its a subclass
                    $oReflectionClass = new ReflectionClass( $subclass );
                    if( !$oReflectionClass->isSubclassOf( $classname ) ){
                        continue;
                    }

                    //Make sure its not abstract
                    if( !$include_abstract ){
                        if( $oReflectionClass->isAbstract() ){
                            continue;
                        }
                    }

                    //Add it to the list
                    $subclasses[] = $subclass;

                }

            }

            return $subclasses;

        }

        public static function loadBoostraps(){

            $paths = self::getConfigPaths();
            foreach( $paths as $path ){
                if( file_exists( $path . "bootstrap.php" ) )
                    include_once( $path . "bootstrap.php" );
            }

        }

        public static function loadConfig(){

            $paths = self::getConfigPaths();
            foreach( $paths as $path ){
                if( file_exists( $path . "config.php" ) )
                    include_once( $path . "config.php" );

                if( file_exists( $path . "database.php" ) )
                    include_once( $path . "database.php" );

                if( file_exists( $path . "routes.php" ) )
                    include_once( $path . "routes.php" );
            }

        }

        public static function loadFunctions(){
            $paths = self::getFunctionsPaths();
            foreach( $paths as $path ){
                $pattern = $path . '*.php';
                $glob    = glob( $pattern );
                foreach( $glob as $file ){
                    require_once( $file );
                }
            }
        }

        public static function loadVendor( $name , $file = null ){

            $vendor_path = $name . DS;

            if( $file === null )
                $vendor_file = $name . '.php';
            else
                $vendor_file = $file;

            $paths = self::getVendorsPaths();

            foreach( $paths as $path ){
                foreach( array( $vendor_file , 'bootstrap.php' ) as $file ){
                    $file = $path . $vendor_path . $file;
                    if( file_exists( $file ) ){
                        require_once( $file );
                        return $path . $vendor_path;
                    }
                }
            }

            trigger_error( "Coundn't load vendor '$name'" , E_USER_ERROR );
            return false;

        }

        public static function version(){
            return self::VERSION;
        }

    }
