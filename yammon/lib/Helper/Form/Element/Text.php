<?php

    class Helper_Form_Element_Text extends Helper_Form_Element_Input{
    		            	    	        	        
        public function setupOptions(){
            parent::setupOptions();
            $this->setOption( 'type' , "text" );
            $this->addClass('ym-form-text form-control');
        }
    
    }
