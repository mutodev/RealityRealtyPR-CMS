<?php

    class Validation_Required extends Validation{
    
        protected function setupOptions(){
            parent::setupOptions();
            $this->setOption( "message" , "%{label} is required" );
        }

        protected function valid( $value , $context = null ){
            return preg_replace('/\s\s+/', ' ', $value ) != '';
        }
    
    }


