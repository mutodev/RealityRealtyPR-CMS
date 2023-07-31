<?php

    class Validation_RegularExpression extends Validation{
        
        protected function setupOptions(){
            parent::setupOptions();
            $this->addOption( "regex" , "//" );
        }        
            
        protected function valid( $value , $context = null ){
            $regex = $this->getOption( "regex" );

            return preg_match( $regex , $value );
        }
    
    }