<?php

    class Validation_Website extends Validation_Url{
    
        protected function setupOptions(){
            parent::setupOptions();
            $this->setOption( "message" , "%{label} is not a valid website" );
        }

        protected function valid( $value , $context = null ){

            if( !parent::valid( $value , $context ) )
                return false;

            return preg_match( "/^http(s)?:\/\//" , $value );
        }
        
        
    
    }


