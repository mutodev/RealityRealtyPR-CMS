<?php

    class Search_Type_Boolean extends Search_Type{

        private $true_values  = array( "1" , "on"  , "true" );
        private $false_values = array( "0" , "off" , "false" );
        
        /* Returns if the value can be considered of the type */
        function is( $value ){
            $value = trim( strtolower( $value ) );
            return in_array( $value , $this->true_values ) || in_array( $value , $this->false_values );
        }

        /* Change Value to meet type standards */
        function value( $value ){

            if( in_array( $value , $this->true_values ) )
                return 1;
            else
                return 0;
            
        }

        /* Returns all the operators for this type */            
        function operators(){
            return array( "Equals" , "NotEquals" );
        }
    
    }
