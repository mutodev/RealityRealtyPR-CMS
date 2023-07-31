<?php

    class Validation_Url extends Validation{
    
        protected function setupOptions(){
            parent::setupOptions();
            $this->setOption( "message" , "%{label} is not a valid url" );
        }

        protected function valid( $value , $context = null ){
            return filter_var( $value , FILTER_VALIDATE_URL ) !== false;
        }
    
    }


