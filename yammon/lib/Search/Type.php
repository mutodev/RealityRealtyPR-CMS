<?php

    class Search_Type{

        public static function factory( $class ){
            $class = "Search_Type_$class";
            return new $class;
        }

        /* Returns if the value can be considered of the type */
        function is( $value ){
            return false;
        }

        /* Change Value to meet type standards */
        function value( $value ){
            return '';
        }

        /* Returns all the operators for this type */            
        function operators(){
            return array();
        }
    
    }
