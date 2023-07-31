<?php

    class Helper_Form_Element_Button_Save extends Helper_Form_Element_Button{
    		    		
        public function setupOptions(){
            parent::setupOptions();
            $this->addOption("image"  , '/yammon/public/form/img/save.gif' );
            $this->setOption("label"  , t("Save") );                
            $this->setOption('class'  , "positive btn-success"  );     
        }                   		         	       	

    }
