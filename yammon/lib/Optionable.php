<?php

    class Optionable{

        protected $_options            = array();
        protected $_options_changed_on = null;

        public function __construct( $options = array() ){
            $this->_options = array();
            $this->_options_changed_on = microtime();
            $this->setupOptions();
            $this->setOptions( $options );
        }

        protected function setupOptions(){

        }

        protected function addOption( $key , $default = null , $translatable = false ){

            if( is_array( $key ) )
                $this->addOptions( $key );
            else
                $this->_options[ $key ] = array(
                    "value"        => $default ,
                    "default"      => $default ,
                    "translatable" => $translatable
                );

            return $default;

        }

        protected function addOptions( $array , $translatable = false ){

            foreach( $array as $key => $value ){
                $this->addOption( $key , $value , $translatable );
            }

        }

        public function getOption( $key , $default = null ){

            if( !$this->isOption( $key ) )
                return $default;

            $option   = $this->_options[ $key ];

            if( $option['value'] === null )
                if( $default !== null )
                    return $default;
                else
                    return $option['default'];
            else
                return $option['value'];

        }

        public function getOptionDefault( $key  ){

            if( !$this->isOption( $key ) )
                return false;

            return $this->_options[ $key ]['default'];

        }

        public function getOptions( ){

            $options = array();
            foreach( $this->_options as $k => $v ){
                $options[ $k ] = $this->getOption( $k );
            }

            return $options;
        }

        public function getOptionsLastChangeTime(){
            return $this->_options_changed_on;
        }

        public function hasOption( $key ){

            if( !$this->isOption( $key ) )
                return false;

            return $this->getOption( $key ) !== null;

        }

        public function isOption( $key ){
            return array_key_exists( $key , $this->_options );
        }

        public function isOptionTranslatable( $key ){

            if( !$this->isOption( $key ) )
                return $false;

            return $this->_options[ $key ]['translatable'];
        }

        public function loadOptions( $filename  ){
            $this->setOptions( $filename );
        }

        public function setOption( $key , $value = null , $overwrite = true ){

            if( is_array( $key ) ){
                $this->setOptions( $key , $overwrite );
            }elseif( $this->isOption( $key ) ){
                if( $overwrite || !$this->hasOption( $key ) ){

                    if( $this->_options[ $key ]['value'] !== $value )
                        $this->_options_changed_on = microtime();

                    $this->_options[ $key ]['value'] = $value;
                }
            }



            return $value;
        }

        public function setOptions( $filename_or_array , $overwrite = true ){


            //Check if we passed a filename
            if( is_array( $filename_or_array ) ){
                $options = $filename_or_array;
            }elseif( is_string( $filename_or_array ) ){

                //Absolutize the filename
                //to the current working directory
                $filename = $filename_or_array;
                if( dirname( $filename ) == '.' ){
                    $filename = getcwd().DS.$filename;
                }

                //Make sure the file exists
                if( !FS::isFile( $filename ) ){
                    throw new Exception("Can't load filename");
                }

                $options = Yaml::load( $filename );

            }else{
                return array();
            }

            foreach( $options as $key => $value ){
                $this->setOption( $key , $value , $overwrite );
            }

            return $options;

        }

        public function setOptionTranslatable( $key , $b = true ){

            $b = (bool)$b;

            if( !$this->hasOption( $key ) )
                return;

            $this->_options[ $key ]['translatable'] = $b;
            return $b;

        }

        protected function registerOption( $key , $default = null , $translatable = false ){
            return $this->addOption( $key , $default );
        }

        protected function registerOptions( $array , $translatable = false ){
            return $this->addOptions( $array , $translatable );
        }

    }

