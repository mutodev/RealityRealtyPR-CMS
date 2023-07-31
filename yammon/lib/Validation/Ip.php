<?php

    class Validation_Ip extends Validation{
    
        protected function setupOptions(){
            parent::setupOptions();
            $this->setOption( "message" , "%{label} is not a valid IP Address" );
        }    
    
        protected function valid( $value , $context = null ){
            return filter_var( $value , FILTER_VALIDATE_IP ) !== false;
        }
    
    }

