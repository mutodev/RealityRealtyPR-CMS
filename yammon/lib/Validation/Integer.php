<?php

    class Validation_Integer extends Validation_RegularExpression{
            
        protected function setupOptions(){
            parent::setupOptions();
            $this->setOption( "message" , "%{label} must be an integer" );
            $this->setOption( "regex"   , "/^\\-?[0-9]+\$/" );
        }        
        
    }

