<?php

    class Console{

        private static $configured   = false;
        private static $base_parser  = null;
        private static $paths        = array();
        private static $index        = -1;
        private static $parsers      = array();
        private static $arguments    = array();
        private static $options      = array();
        private static $canceled     = false;
        private static $cli          = null;

        private function __construct(){

        }

        public static function signal_handler( $signal )
        {
            switch( $signal ){
                case SIGTERM:
                case SIGKILL:
                case SIGINT:
                                self::$canceled = true;
                                break;
            }

        }

        private static function configure( $path , $Command = null , $root = true , $prefix = null )
        {

            $basename  = basename( $path );
            $conf_file = $path."console.yml";
            $file_name = $path.$basename.".php";

            if( !file_exists( $conf_file ) )
                return;

            if( !file_exists( $file_name ) )
                return;

            //Read the variables out of the YAML
            $yaml        = Yaml::load( $conf_file );
            $name        = isset( $yaml['name'] )        ? $yaml['name']          : basename( $path );
            $version     = isset( $yaml['version'] )     ? $yaml['version']       : '1.0';
            $description = isset( $yaml['description'] ) ? $yaml['description']   : '';
            $options     = isset( $yaml['options'] )     ? $yaml['options']       : array();
            $arguments   = isset( $yaml['arguments'] )   ? $yaml['arguments']     : array();


            //Change the name
            $path_name  = $prefix ? $prefix.".".$name : $name;


            unset( $yaml['name'] );
            unset( $yaml['options'] );
            unset( $yaml['arguments'] );

            //Configure Root

            if( $root ){
                $SubCommand = null;
                self::$base_parser->version     = $version;
                self::$base_parser->description = $description;
            }elseif( $Command === null ){
                $SubCommand = self::$base_parser->addCommand( $name , $yaml );
                $SubCommand->description = $description;
                $SubCommand->version     = $version;
            }else{
                $SubCommand = $Command->addCommand( $name , $yaml );
                $SubCommand->description = $description;
                $SubCommand->version     = $version;
            }

            //Configure Children
            $directories = FS::findDirectories( "*" , false , $path );
            foreach( $directories as $directory ){
                self::configure( $directory , $SubCommand , false , $path_name );
            }

            //Add Options
            foreach( $options as $k => $v ){
                $SubCommand->addOption( $k , $v );
            }

            //Add Arguments
            foreach( $arguments as $k => $v ){
                $SubCommand->addArgument( $k , $v );
            }

            //Save the path
            self::$paths[ $path_name ] = $file_name;

        }

        public static function run( $command = null ){

            //Get the console path
            $path = APPLICATION_PATH . 'console' . DS;

            //Configure Console
            if( !self::$configured ){

                //Create the base parser
                self::$base_parser = new Console_CommandLine();

                //Configure Recursively
                self::configure( $path );

                //TODO: Configure signals
                if( self::cli() ){
                    if( function_exists("pcntl_signal") ){
                        pcntl_signal( SIGTERM , array( "Console" , "signal_handler" ) );
                        pcntl_signal( SIGHUP  , array( "Console" , "signal_handler" ) );
                        pcntl_signal( SIGINT  , array( "Console" , "signal_handler" ) );
                    }
                }

                //Mark as configured
                self::$configured = true;

            }

            //Add to the stack
            self::$index++;

            //Start request stack
            $parser = clone( self::$base_parser );
            self::$parsers[   self::$index ] = $parser;
            self::$arguments[ self::$index ] = array();
            self::$options[   self::$index ] = array();

            //Create command
            if( trim($command) === '' ){
                $argv = null;
                $argc = null;
            }else{
                $argv = preg_split("/[\s]+/", 'console.php '. $command );
                $argc = count( $argv );
            }

            //Get the Arguments
            $Arguments = array();
            try {
                $Arguments = $parser->parse(  $argc , $argv );
            } catch (Exception $exc) {
                $parser->displayError($exc->getMessage());
                exit;
            }

            //Check that we have a command
            if( !$Arguments->command_name )
                Console::usage();

            //Find the arguments in the nested struct
            $command_name     = 'console';
            $CommandArguments = $Arguments;
            while( true ){
                if( !$CommandArguments->command_name )
                    break;

                $command_name     = $command_name ? $command_name . "." . $CommandArguments->command_name : $CommandArguments->command_name;
                $CommandArguments = $CommandArguments->command;
            }

            //Set arguments and options for current command
            self::$arguments[ self::$index ] = $CommandArguments->args;
            self::$options[ self::$index ]   = $CommandArguments->options;

            //Dispatch
            self::dispatch( self::$paths[ $command_name ] );

            //Decrement Stack
            self::$index--;
            array_pop( self::$parsers );
            array_pop( self::$arguments );
            array_pop( self::$options );


        }

        private static function dispatch( $__FILE__ )
        {
            require_once( $__FILE__ );
        }

        public static function argument( $key , $default = null )
        {
            if( isset( self::$arguments[ self::$index ][$key] ) )
                return self::$arguments[ self::$index ][$key];
            else
                return $default;

        }

        public static function option( $key , $default = '' )
        {
            if( isset( self::$options[ self::$index ][$key] ) )
                return self::$options[ self::$index ][$key];
            else
                return $default;
        }

        public static function usage( $exitCode = 0 )
        {
            if( self::$base_parser )
                self::$base_parser->displayUsage( $exitCode );
        }

        public static function read( $prompt = null )
        {
            return Console_IO::read( $prompt );
        }

        public static function write( $string = '' , $endline = true , $colorize = true )
        {
            return Console_IO::write( $string , $endline , $colorize );
        }

        public static function writeLine( $string = '' , $colorize = true )
        {
            return Console_IO::writeLine( $string , true , $colorize  );
        }

        public static function writeError( $string , $endline = true )
        {
            return Console_IO::writeError( $string , $endline = true );
        }

        public static function writeErrorLine( $string = '' )
        {
            return Console_IO::writeErrorLine( $string );
        }

        public static function cli()
        {
            if( self::$cli === null ){
                self::$cli = (php_sapi_name() == "cli");
            }

            return self::$cli;

        }

        public static function cancel()
        {
            return self::$canceled = true;
        }

        public static function canceled()
        {
            //Check if we are in cli mode
            $cli = self::cli();

            //Check if we were explicitly canceled
            if( self::$canceled )
                return true;

            //Check if the connection was aborted
            if( !$cli ){
                if( connection_status() != CONNECTION_NORMAL )
                    return true;
            }

            //We haven't canceled
            return self::$canceled;
        }

    }
