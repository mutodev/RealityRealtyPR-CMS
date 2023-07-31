<?php

    class Helper_Form_Element_Button_Previous extends Helper_Form_Element_Button{
    		    		
        public function setupOptions(){
            parent::setupOptions();
            $this->setOption("label" , t("Previous") );
            $this->setOption("image" , "/yammon/public/form/img/previous.gif" );
        }                   		         	       	
    
    }
