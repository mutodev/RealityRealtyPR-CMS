<?php

    class Search_Type_String extends Search_Type{
        
        /* Returns if the value can be considered of the type */
        function is( $value ){
            return true;
        }

        /* Change Value to meet type standards */
        function value( $value ){
            return $value;
        }

        /* Returns all the operators for this type */            
        function operators(){
            return array( "Contains" , "NotContains"  , "Equals" , "NotEquals" );
        }
    
    }
