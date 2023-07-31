<?php

    abstract class Validation extends Optionable{
                  
        protected function setupOptions(){
            $this->addOption( "message" , "%{label}' is not valid" , true );        
        }
            
        public function message( $arguments = array() ){
            return $this->getMessage( $arguments );
        }

        public function validate( $value , $context = array() ){
            return $this->valid( $value , $context );
        }

        public function getMessage( $arguments = array() ){
            $arguments = array_merge( $this->getOptions() , $arguments  );
            $message   = $this->getOption("message");
            $Template  = new Template( $message );
            
            return $Template->apply( $arguments );
        }

        public function getTranslationStrings(){
        
            $strings = array();
            $message = $this->getOption('message');
            if( $message ) $strings[] = $message;
            
            return $strings;
        
        }

        abstract protected function valid( $value , $context = null );
    }
