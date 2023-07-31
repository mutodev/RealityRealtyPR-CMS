<?php

    class Validation_Numeric extends Validation{
    
        protected function setupOptions(){
            parent::setupOptions();
            $this->setOption( "message" , "%{label} must be a number" );
        }

        protected function valid( $value, $context = null ){
            return ( is_numeric($value) );
        }
    
    }
