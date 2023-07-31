<?php

    class Filter_PostalCode extends Filter{
        
        public function filter( $value ){

            $value   = trim( $value );
            $value   = preg_replace("[^0-9]" , "" , $value );
            
            if( $value == "" ){
                return $value;
            }elseif( strlen( $value ) > 5 ){
                return substr( $value , 0 , 5 );
            }else{
                return str_pad( $value , 5 , "0" , STR_PAD_LEFT );
            }
                        
        }
    
    }
