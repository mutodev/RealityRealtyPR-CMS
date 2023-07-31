<?php

    class View extends Collection{

        protected $file = null;

        public function __construct( $file = null ){
            parent::__construct();
            $this->setFile( $file );
        }

        public function setFile( $file ){
            $this->file = $file;
        }

        public function getFile(){

            $file = $this->file;
            if( substr( $file , strlen(".phtml")*-1) !== ".phtml" )
                $file = $file.".phtml";

            return $file;
        }

        public function partial( $file , $arguments = array() ){

            //Add the current directory to the paths
            $paths     = array();
            $paths[]   = realpath( dirname($file)."/" );
            $paths[]   = dirname( $this->file )."/";

            //Get the system view path
            $paths    = array_merge( $paths , Yammon::getViewsPaths() );

            //Get the filename
            $filename = basename( $file );
            if( substr( $filename , strlen(".phtml")*-1) !== ".phtml" )
                $filename = $filename . ".phtml";

            //Look for the filename in the paths
            $real_file = null;
            foreach( $paths as $path ){
                if( !$path ) continue;

                if( substr( $path , -1 , 1 ) != "/" )
                    $path = $path."/";

                $possible_file = $path . $filename;
                if( FS::isFile( $possible_file ) ){
                    $real_file = $possible_file;
                    break;
                }
            }

            //Render the partial
            $action = dirname( $real_file ). DS . basename( $real_file , '.phtml' ).".php";
            if( FS::isFile( $action ) )
                $arguments = $this->component($action, $arguments);

            return $this->parse( $real_file , $arguments );
        }

        public function render( $file = null ){
            if( !empty( $file ) ){
                $this->setFile( $file );
            }
            return $this->parse( $this->getFile() , $this->toArray() );
        }

        protected function component( ){

            //Export Variables
            if( func_num_args() > 1 )
              extract( func_get_arg(1) );

            include( func_get_arg( 0 ) );

            return get_defined_vars();
        }

        protected function parse( ){

            //Export Variables
            if( func_num_args() > 1 )
              extract( func_get_arg(1) );

            //Check for the existance of the file
            if( !FS::isFile( func_get_arg( 0 ) ) ){
                throw new View_NotFoundException( "Coudn't find view '". func_get_arg( 0 ) ."'" );
            }else{
                //Get contents of file
                ob_start();
                include( func_get_arg( 0 ) );
                return ob_get_clean();
            }

        }

    }
