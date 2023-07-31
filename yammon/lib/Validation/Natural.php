<?php

    class Validation_Natural extends Validation_Integer{
            
        protected function setupOptions(){
            parent::setupOptions();
            $this->setOption( "message" , "%{label} must be valid integer greater then 0" );
            $this->setOption( "regex"   , "/^\\-?[0-9]+\$/" );
        }        
        
        protected function valid( $value , $context = null )
        {
        
            if( !parent::valid( $value , $context ) )
                return false;
                
            return $value >= 0;                
        }
            
    }
    