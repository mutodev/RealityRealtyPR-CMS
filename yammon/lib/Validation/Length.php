<?php
    
    class Validation_Length extends Validation{
            
        protected function setupOptions(){
            parent::setupOptions();
            $this->setOption( "message" , "%{label} incorrect length" );
            $this->addOption( "length"  , 0 );
        }        
        
        protected function valid( $value , $context = null ){
            $length = $this->getOption('length');
            return strlen( $value ) == $length;
        }
        
        
    }

