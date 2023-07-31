<?php

    class Helper_Form_Element_Button_Cancel extends Helper_Form_Element_Button{
    		         	       	
        public function setupOptions(){
            parent::setupOptions();
            $this->addOption("image"  , '/yammon/public/form/img/cancel.gif' );
            $this->setOption("label" , t("Cancel") );
            $this->setOption("href"  , ".." );
            $this->setOption( 'class', 'negative btn-danger' );
        }          
                    
                        
    }
