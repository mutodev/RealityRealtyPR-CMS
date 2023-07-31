<?php

    class Helper_Form_Element_Button_Less extends Helper_Form_Element_Button_Repeat{
    		    		
        public function setupOptions(){
            parent::setupOptions();
            $this->setOption("label" , t("-") );                 
            $this->addClass('ym-form-repeat-less');            
        }                   		         	       	
    
    }
