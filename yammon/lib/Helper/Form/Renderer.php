<?php

    abstract class Helper_Form_Renderer{

        private static $registry = array();

        private function __construct(){

        }

        public function __toString(){
            return $this->render();
        }

        static protected function factory( $base  , $type ){
                        
            //Get the class
            $class = "Helper_Form_Renderer_".ucfirst($base)."_".ucfirst($type);

            //Add to the registry
            if( !isset( self::$registry[ $class ] ) )
                self::$registry[ $class ] = new $class();
            
            //Return the classs
            return self::$registry[ $class ];            
            
        }
        
        abstract public function render( $element , $options );
    
    }
