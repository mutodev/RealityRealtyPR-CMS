<?php

    class Helper_Form_Element_Date extends Helper_Form_Element_DateTime{
    		     	  
        public function setupOptions(){
            parent::setupOptions();
            $this->setOption('format' , '%M/%d/%Y %p' );    
        }
        
    }

