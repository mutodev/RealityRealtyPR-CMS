<?php

    class Validation_Email extends Validation{

        protected function setupOptions(){
            parent::setupOptions();
            $this->setOption( "message" , "%{label} is not a valid email" );
        }

        protected function valid( $value , $context = null ){
            return filter_var($value, FILTER_VALIDATE_EMAIL);
        }

    }


