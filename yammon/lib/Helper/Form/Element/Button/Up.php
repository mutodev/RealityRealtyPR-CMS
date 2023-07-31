<?php

    class Helper_Form_Element_Button_Up extends Helper_Form_Element_Button_Repeat{
    		    		
        public function setupOptions(){
            parent::setupOptions();
            $this->setOption("label" , t("&uarr;") );                 
            $this->addClass('ym-form-repeat-up');            
        }                   		         	       	
    
    }
