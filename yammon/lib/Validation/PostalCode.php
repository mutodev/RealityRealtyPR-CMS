<?php

    class Validation_PostalCode extends Validation{
            
        protected function setupOptions(){
            parent::setupOptions();
            $this->setOption( "message" , "%{label} is not a valid postal code" );
        }        

        protected function valid( $value, $context = null ){
        
            $v = preg_replace( "[^0-9]" , "" , $value );
            
            if( $value !== $v ){
                return false;
            }elseif( strlen( $value ) == 5 ){
                return true;
            }else{
                return false;
            }
            
        }
    
    }
