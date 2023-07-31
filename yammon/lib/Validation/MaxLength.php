<?php

    class Validation_MaxLength extends Validation{

        protected function setupOptions(){
            parent::setupOptions();
            $this->setOption( "message" , "%{label} is to long" );
            $this->addOption( "length"  , 0 );
        }

        protected function valid( $value , $context = null ){
            $length = $this->getOption('length');
            return strlen( $value ) <= $length;
        }


    }

