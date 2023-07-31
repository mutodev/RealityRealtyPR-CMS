<?php

    class Router{

        static protected $routes          = array();
        static protected $action          = array();
        static protected $default         = null;
        static protected $params          = array();

        static public function actionExists( $action ){
            $path = self::getActionPath( $action );
            return $path !== null;
        }

        /**
         * Connects an url with an action
         * after this method is called errors and warnings will be available
         *
         * @param  string $pattern    a pattern that represents the url to connect
         * @param  string $action     the action the url connects to
         * @param  string $parameters if you want you can specify an array of key value pairs of how to map the paramters
         * @return Route
        */
        static public function connect( $pattern , $action , $requirements = array() , $methods = array() ){
            $route = new Route( $pattern , $action , $requirements , $methods );
            self::$routes[] = $route;
            return $route;
        }

        static public function connectDefault( $action ){
            self::$default = $action;
        }

        static protected function dispatch( $action  , $arguments = array() , $forward = false ){

            //Parse the action
            list( $action , $action_query , $fragment ) = Router::parseAction( $action );

            //Get the action file
            $action_file = self::getActionFile( $action );

            //Make sure that the action exists
            if( $action_file === null ){
                self::forwardNotFound();
                return false;
            }

            //Save the current action
            if( !$forward || empty( self::$action) )
                self::$action = $action;

            //Inject arguments into get
            $_GET = array_merge( $action_query , $arguments , $_GET );

            //Run the action
            Action::run( $action_file );

            return true;

        }

        static public function forward( $action , $arguments = array() , $clear = false , $exit = true ){

            //Save the request
            $GET     = $_GET;
            $POST    = $_POST;
            $REQUEST = $_REQUEST;
            $FILES   = $_FILES;

            //Clear the request
            if( $clear ){
                $_GET     = array();
                $_POST    = array();
                $_REQUEST = array();
                $_FILES   = array();
            }

            //Dispatch the action
            self::dispatch( $action , $arguments , true );

            //Restore Request
            $_GET     = $GET;
            $_POST    = $POST;
            $_REQUEST = $REQUEST;
            $_FILES   = $FILES;

            //Exit
            if( $exit )
                exit();

        }

        static public function forwardError( $error = 500  ){

            $actions     = array();
            $description = "";

            //Validate error
            if( !is_numeric( $error ) )
                $error = 500;

            if( $error == 404 ){
                $actions[]   = "error.notfound";
                $actions[]   = "error.404";
                $description = "Not Found";
            }elseif( $error == 403 ){
                $actions[]   = "error.forbidden";
                $actions[]   = "error.403";
                $description = "Forbidden";
            }elseif( $error == 500 ){
                $actions[]   = "error.500";
                $actions[]   = "error";
                $description = "Internal Server Error";
            }else{
                $actions[]   = "error".$error;
                $actions[]   = "error";
                $description = ' ';
            }

            //Set Header
            header($_SERVER['SERVER_PROTOCOL'] . ' ' . $error . ' ' . $description);

            //Find an action to display the error
            foreach( $actions as $action ){
                if( self::actionExists( $action) ){
                    self::forward( $action , array() , false );
                }
            }

            //Display the error ourselves
            echo  "$error $description";
            exit();

        }

        static public function forwardForbidden(  ){
            return self::forwardError( 403 );
        }

        static public function forwardNotFound(  ){
            return self::forwardError( 404 );
        }

        static public function getPaths(){
            return Yammon::getActionsPaths();
        }

        static public function getActionFile( $action = null ){

            if( $action === null )
                $action = self::getCurrentAction();

            //Explode the action
            $action_path = explode( "." , $action );
            foreach( $action_path as $k => $v ){
                if( $v == '' ) unset( $action_path[ $k ] );
            }

            //Get the basename
            $controller = array_shift( $action_path );
            $basename   = array_pop( $action_path );

            //Get the possible files
            $files = array();
            $paths = self::getPaths();

            foreach( $paths as $path ){

                if( !$controller ){
                    $files[] = $path . "index.php";
                    $files[] = $path . "index".DS."index.php";
                }elseif( !$basename ){
                    $files[] = $path . $controller . DS . $controller. ".php";
                    $files[] = $path . $controller . DS . "index.php";
                }elseif( $action_path ){
                    $files[] = $path . $controller . DS . implode( DS  , $action_path ) . DS . $basename . DS . $basename. ".php";
                    $files[] = $path . $controller . DS . implode( DS  , $action_path ) . DS . $basename . DS . "index.php";
                }else{
                    $files[] = $path . $controller . DS . $basename . DS . $basename. ".php";
                    $files[] = $path . $controller . DS . $basename . DS . "index.php";
                }

            }

            //Find the correct file
            foreach( $files as $file ){
                if( FS::isFile( $file ) ){
                    return realpath( $file );
                }
            }

            return null;

        }


        static public function getActionPath( $action = null ){

            $file = self::getActionFile( $action );
            if( $file === null )
                return null;
            else
                return dirname( $file ).DS;

        }

        static public function getCurrentAction( ){
            return self::$action;
        }

        static public function getCurrentActionPath(){
            $action = self::getCurrentAction( );
            return self::getActionPath( $action );
        }

        static public function getParentAction( $action = null ){
            if( $action == null )
                $action = self::getCurrentAction();

            $action = explode( "." , $action );
            array_pop( $action );
            $action = implode( "."  , $action );

            return $action;
        }

        static public function parseUrl( $url  ){

            //Remove query string
            $url = preg_replace( "/\?.*$/" , "" , $url );

            //Get the arguments out of the url
            $url_parts       = explode( "/" , $url );

            //Remove empty path
            foreach( $url_parts as $k => $url_part ){
                if( $url_part == '' )
                    unset( $url_parts[$k] );
            }

            $url_transformed = array();
            $url_arguments   = array();
            foreach( $url_parts as $url_part ){

            	$token_pos = strpos( $url_part , ":" );

                if( $token_pos !== false ){

                    $url_key   = substr(  $url_part , 0 , $token_pos );
                    $url_value = substr(  $url_part , $token_pos + 1 );
                    if( !isset( $_GET[ $url_key ] ) )
                      $url_arguments[ $url_key ] = urldecode( $url_value );

                }else{
                    $url_transformed[] = $url_part;
                }

            }

            //Recreate the url
            $url = implode( "/" , $url_transformed );
            if( empty( $url ) ) $url = "/";

            //Return
            return array( $url , $url_arguments );

        }

        static public function parseAction( $action , $args = array() ){

            //Check if the url is an array
            $action_query = array();
            if( is_array( $action  ) ){
                $action_query = $action;
                $action       = Router::getCurrentAction();
            }

            //Apply Arguments
            $template = new Template( $action );
            foreach( $args as $i => $arg ){

                //Normalize the argument
                if( !is_array( $arg ) && !is_object( $arg ) ){
                    $arg = array( $i => $arg );
                }

                $action = $template->apply( $args );
            }

            //Remove the query and fragment from the action
            $fragment          = "";
            $fragment_position = strrpos( $action , "#" );
            if( $fragment_position !== false ){
                $fragment = substr( $action ,     $fragment_position + 1   );
                $action   = trim(substr( $action , 0 , $fragment_position ));
            }

            //Remove the query from the action
            $query          = array();
            $query_position = strrpos( $action , "?" );
            if( $query_position !== false ){
                $query   = substr( $action , $query_position + 1  );
                parse_str( $query , $query );
                if( $query_position == 0 ){
                    $action = ".";
                }else{
                    $action  = substr( $action , 0 , $query_position  );
                }
            }

            //Check if its a relative action
            $len = strlen( $action );
            for( $relative = 0 ; $relative < $len ; $relative++ ){
                if( $action{ $relative } != '.' ){
                    break;
                }
            }

            //Expand the action
            if ( strpos( $action , "." ) === 0 ) {

                $actionTmpArray = explode('.' , Router::getCurrentAction() );

                for( $dots = 1; substr($action, $dots, 1) == '.'; $dots++) {
                    array_pop($actionTmpArray);
                }

                $action = substr($action, $dots-1);
                $action = implode('.', $actionTmpArray) . $action;
            }

            if( substr( $action , -1 ,1 ) == '.' ){
                $action = substr( $action , 0 , -1 );
            }

            //Make the action canonical
            $action = strtolower( $action );
            $action = explode("." , $action );
            foreach( $action as $k => $v ){
                $v = trim($v);
                if( $v === "index" ){
                    unset( $action[ $k ] );
                }else{
                    $action[ $k ] = $v;
                }
            }
            $action = implode( "." , $action );

            //Merge the queries
            $query = array_merge( $query , $action_query );

            //Return the action parts
            return array( $action , $query , $fragment );

        }

        static public function route( $url = null ){

            //Use the current url if not specified
            if( empty( $url ) ){
                $url = @$_SERVER["REQUEST_URI"];
            }

            //Parse the url
            list( $url , $url_arguments ) = self::parseUrl( $url );
            self::$params = $url_arguments;

            //Attempt to find connected route
            foreach( self::$routes as $route ){
                $action = $route->matchesUrl( $url );
                if( $action !== false ){
                    if( self::dispatch( $action , $url_arguments ) ){
                        return true;
                    }
                }
            }

            //Get the requested action
            if( defined('APPLICATION_PREFIX') ){
                $url_exploded = explode( "/" , substr( $url  , strlen(APPLICATION_PREFIX)) );
            }else{
                $url_exploded = explode( "/" , $url );
            }

            foreach( $url_exploded as $k => $v ){
                if( $v == "" ) unset( $url_exploded[ $k ] );
            }
            $action = implode( "." , $url_exploded );

            //Dispatch
            if( self::getActionFile( $action ) ){
                if( self::dispatch( $action , $url_arguments ) ){
                    return true;
                }
            }elseif( self::$default ){
                if( self::dispatch( self::$default , $url_arguments ) ){
                    return true;
                }

            }

            //If we got here we didn't find a route
            self::forwardNotFound();
            return false;

        }

        static public function url( $action = null ){

            $args   = func_get_args();
            $action = array_shift( $args );

            //Check if the url is absolute
            if( is_string( $action  ) && strpos( $action  , "/" ) !== false ){
                return $action;
            }

            //Parse the action
            list( $action , $query , $fragment ) = Router::parseAction( $action , $args );

            //Try to find a reverse route
            foreach( self::$routes as $route ){
                $url = $route->matchesAction( $action , $query );
                if( $url !== false ){
                    return $url;
                }
            }

            //Assemble the url
            return self::buildUrl( $action , $query , $fragment );

        }

        static function buildUrl( $action , $query = null , $fragment = null , $scheme = null , $host = null , $port = null ){

             $url = "";

             //Get the default values
             if( empty( $scheme ) )
                $scheme = !empty($_SERVER['HTTPS']) ?  "https://" : "http://";

             if( empty( $host ) ){
                $host = @$_SERVER['HTTP_HOST'];
             }

             if( empty( $port ) ){
                $port   = @$_SERVER['SERVER_PORT'];
             }

             if( empty( $query ) ){
                $query = array();
             }elseif( !is_array( $query ) ){
                $query = array();
             }

             //Create the path
             $path = explode("." , $action );
             foreach( $query as $k => $v ){
                if( !is_array( $v ) ){
                    $path[] = urlencode($k).":".urlencode($v);
                    unset( $query[ $k ] );
                }
             }

             foreach( $path as $k => $v ){
                if( $v == "" ) unset( $path[ $k] );
             }

             if( defined('APPLICATION_PREFIX')){
                if( !$path )
                    $path = APPLICATION_PREFIX;
                else
                    $path = APPLICATION_PREFIX.'/'.implode( "/" , $path );
             }else{
                $path = implode( "/" , $path );
             }

             //Assemble url
             $url  = $scheme;
             $url .= $host;

            $port = '';

             if( !empty($port) ){
                if( ($scheme == "http://" && $port != 80) || ($scheme == "https://" && $port != 443 ) ){
                    $url .= ':'.$port;
                }
             }

             if( !empty( $path ) ){
                $url .= "/".$path."/";
             }

             if( !empty( $query ) ){
                $url .= '?'.http_build_query( $query );
             }

             if( !empty( $fragment ) ){
                $url .= '#'.$fragment;
             }

             return $url;

        }

        static public function getNamedParameter( $key , $default = null ){
            return input( self::$params , $key , $default );
        }

        static public function getNamedParameters(){
            return self::$params;
        }

        /* Compablity Functions because of inconrrect spelling */
        static public function foward( $action , $arguments = array() , $clear = false , $exit = true ){
            return self::forward( $action , $arguments , $clear );
        }

        static public function fowardError( $error = 500  ){
            return self::forwardError( $error );
        }

        static public function fowardForbidden(  ){
            return self::forwardForbidden(  );
        }

        static public function fowardNotFound(  ){
            return self::forwardNotFound( );
        }

    }
