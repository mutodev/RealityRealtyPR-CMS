<?php

    abstract class Component extends Optionable
    {
        private $component_name = null;    
        private static $instances = array();

        public function __construct( $name , $options = array() ){               
            $this->setName( $name );
            parent::__construct( $options );
        }
                
        protected static function _factory( $base_class , $subclass , $name = null , $options = array() ){
        
            //Get the class of the object to construct
            $basename  = ucfirst( $subclass );
            $subclass  = $base_class."_".$basename;
            
            //Check if the class exists
            if( !class_exists( $subclass ) )
                return null;
        
            //Set the default name
            if( $name === null ){
            
                //Check if there is an object for that class
                if( !empty( self::$instances[ $base_class ][ $subclass ] ) ){
                    $keys = array_keys( self::$instances[ $base_class ][ $subclass ] );
                    $name = array_shift( $keys );                 
                }else{
                    $name = $basename;
                }
                
            }                
            
            //Check if an instance with that name already exists
            if( isset( self::$instances[ $base_class ][ $subclass ][ $name ] ) )
                return self::$instances[ $base_class ][ $subclass ][ $name ];
                
            //Create the object
            $object = new $subclass( $name , $options );
            
            //Save the object
            self::$instances[ $base_class ][ $subclass ][ $name ] = $object;
                
            //Return
            return $object;
        
        }
                
        protected static function _getInstances( $base_class , $first = false ){

            $return     = array();
            $instances  = isset( self::$instances[ $base_class ] ) ? self::$instances[ $base_class ]  : array();
            
            foreach( $instances as $class => $objects ){
                foreach( $objects as $object ){
                    $return[] = $object;
                    if( $first ) break;
                }
            }
        
            return $return;
        
        }
               
        public function construct( ){
            
        }               
                            
        public static function factory( $subclass , $name = null , $options = array() ){
            return self::_factory( 'Component' , $subclass , $name , $options );
        }

        public static function getInstances( $first = false ){
            return self::_getInstances( 'Component' , $first );
        }
        
        public function getName( ){
            return $this->component_name;        
        }        
                
        public function setName( $name ){

            $class      = get_class( $this );
            $base_class = substr( $class , strpos( $class , "_" ) + 1 );
                        
            //Check that the name is not empty
            if( trim( $name ) == '' ){
                throw new Exception( "Name for $base_class '$class' can't be empty");
            }
            
            //If the name is the same don't do anything
            if( $name == $this->component_name ){
                return $name;
            }

            //Check if there is another helper with the same name  
            if( isset( self::$instances[ $base_class ][ $class ][ $name ] ) ){
                throw new Exception("The name '$name' is already taken by another $base_class of type '$class'");
            }
                        
            //Remove old component name
            $instance = false;
            if( isset( self::$instances[ $base_class ][ $class ][ $this->component_name ] ) )
                $instance = self::$instances[ $base_class ][ $class ][ $this->component_name ];
                
            if( $instance ){
                unset( self::$instances[ $base_class ][ $class ][ $this->component_name ] );
                self::$instances[ $base_class ][ $class ][ $name ] = $instance;
            }                

            //Set the name and return
            return $this->component_name = $name;

        }        
        
    }

