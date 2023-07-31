<?php

    class Search_Type_Number extends Search_Type{
        
        /* Returns if the value can be considered of the type */
        function is( $value ){
            return is_numeric( $value );
        }

        /* Change Value to meet type standards */
        function value( $value ){
            return $value;
        }

        /* Returns all the operators for this type */            
        function operators(){
            return array( "Equals" , "NotEquals" , "GreaterThan" , "LessThan" , "GreaterThanOrEqual" , "LessThanOrEqual" );
        }
    
    }
