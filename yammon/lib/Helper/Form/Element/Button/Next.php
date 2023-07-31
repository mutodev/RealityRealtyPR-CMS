<?php

    class Helper_Form_Element_Button_Next extends Helper_Form_Element_Button{
    		    		
        public function setupOptions(){
            parent::setupOptions();
            $this->setOption("label" , t("Next") );
            $this->setOption("image" , "/yammon/public/form/img/next.gif" );
        }                   		         	       	
    
    }
