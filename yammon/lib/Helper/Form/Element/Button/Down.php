<?php

    class Helper_Form_Element_Button_Down extends Helper_Form_Element_Button_Repeat{
    		    		
        public function setupOptions(){
            parent::setupOptions();
            $this->setOption("label" , t("&darr;") );                 
            $this->addClass('ym-form-repeat-down');            
        }                   		         	       	
    
    }
