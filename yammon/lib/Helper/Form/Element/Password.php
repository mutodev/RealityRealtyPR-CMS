<?php

    class Helper_Form_Element_Password extends Helper_Form_Element_Input{
    		            	    	        	        
        public function setupOptions(){
            parent::setupOptions();
            $this->setOption( 'type' , "password" );
            $this->addClass('ym-form-text');
            $this->addClass('ym-form-password');            
        }
    
    }
