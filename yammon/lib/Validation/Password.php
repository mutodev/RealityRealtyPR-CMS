<?php

    class Validation_Password extends Validation{

        protected function setupOptions(){
            parent::setupOptions();
            $this->setOption( "message" , "%{label} is not a valid password" );
            $this->addOptions( array(
                "strong"     => false ,
                "space"      => false ,
                "min"        => 8 ,
                "max"        => null ,
                "letters"    => null ,
                "uppercase"  => null ,            
                "numbers"    => null ,            
                "symbols"    => null ,               
            ));
        }

        protected function valid( $value , $context = null ){

            $strong    = $this->getOption('strong');   
            $space     = $this->getOption('space');   
            $min       = $this->getOption('min');
            $max       = $this->getOption('max');
            $letters   = $this->getOption('letters');
            $uppercase = $this->getOption('letters');            
            $numbers   = $this->getOption('numbers');
            $symbols   = $this->getOption('symbols');            


            //Set the defaults if its a strong password
            if( $strong ){
                if( $space     === null ) $space     = false;            
                if( $min       === null ) $min       = 8;
                if( $letters   === null ) $letters   = 1;
                if( $uppercase === null ) $uppercase = 1;
                if( $numbers   === null ) $numbers   = 1;
                if( $symbols   === null ) $symbols   = 1;                
            }
            
            //Check the password
            $matches = array();
            
            if( $space === false ){
                if( preg_match_all( "/\s/" , $value , $matches ) ){
                    return false;
                }
            }
            
            if( $min ){
                if( strlen( $value ) < $min  ){                
                    return false;
                }
            }

            if( $max ){
                if( strlen( $value ) > $max  ){                                
                    return false;
                }
            }

            if( $letters ){
                if( preg_match_all( "/[A-Z]/i" , $value , $matches ) < $letters ){                
                    return false;
                }
            }

            if( $uppercase ){
                if( preg_match_all( "/[A-Z]/" , $value , $matches ) < $uppercase ){                             
                    return false;
                }
            }

            if( $numbers ){
                if( preg_match_all( "/[0-9]/" , $value , $matches ) < $numbers ){      
                    return false;
                }
            }
                        
            if( $symbols ){
                if( preg_match_all( "/[^A-Z0-9]/i" , $value , $matches ) < $symbols ){                
                    return false;
                }
            }            
            
            return true;

        }
    
    }


