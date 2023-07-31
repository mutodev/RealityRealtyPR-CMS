<?php

    class Search_Type_Date extends Search_Type{

        /* Returns if the value can be considered of the type */
        function is( $value ){
            return strtotime( $value ) !== false;
        }

        /* Change Value to meet type standards */
        function value( $value ){

			if( is_numeric($value) ) {
                return date("Y-m-d H:i:s", $value);
            }

            return $value;
        }

        /* Returns all the operators for this type */
        function operators(){
            return array( "Equals" , "NotEquals" , "Before" , "After" );
        }

    }
