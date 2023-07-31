<?php

    class Helper_Form_Element_Website extends Helper_Form_Element_Input{       	    		
       	   
        public function setupOptions(){
            parent::setupOptions();
            $this->setOption('type' , 'url' );
            $this->addValidation('website');
            $this->addClass('ym-form-text');
            $this->addClass('ym-form-url');   
        }       	   
       	   
        public function getValue(){

            //Append http if value doesn't have it
            $value = parent::getValue();
            if( $value && !preg_match( "/^http(s)?:\/\//" , $value ) )
                $value = "http://".$value;
            return $value;                
        }
       	   
    }
