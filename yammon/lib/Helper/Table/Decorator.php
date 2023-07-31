<?php

    abstract class Helper_Table_Decorator extends Optionable{

        /**
            Constructor
        **/
        function __construct( $options = array() ){
            parent::__construct( $options );
        }

        /**
            Create a new Table Decorator
        **/
        static function create( $name , $options = array() ){

            $name  = ucfirst( $name );
            $class = "Helper_Table_Decorator_".$name;

            if( !class_exists( $class ) ){
                throw new Helper_Table_DecoratorNotFoundException("Cound't find table decorator '$name'");
            }

            return new $class( $options );

        }

        /**
            Apply Decorator
        **/
        abstract function apply( $value );

    }
