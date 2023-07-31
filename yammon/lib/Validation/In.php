<?php

    class Validation_In extends Validation{
        
        protected function setupOptions(){
            parent::setupOptions();
            $this->addOption( "values" , array() );
            $this->addOption( "strict" , false );            
        }        
            
        protected function valid( $value , $context = null ){
            $strict = (bool)$this->getOption( "strict" );
            $values = (array)$this->getOption( "values" );            
            return in_array( $value , $values , $strict );
        }
    
    }