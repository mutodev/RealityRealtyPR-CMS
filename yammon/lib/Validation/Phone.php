<?php

    class Validation_Phone extends Validation{

        protected function setupOptions(){
            parent::setupOptions();
            $this->setOption( "message" , "%{label} is not a valid phone number" );
        }

        protected function valid( $value, $context = null ){
            $value = trim( $value );
            $value = preg_replace("/[^0-9]/" , "" , $value );
            return strlen( $value ) >= 10;
        }

    }
