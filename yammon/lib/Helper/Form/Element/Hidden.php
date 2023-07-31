<?php

    class Helper_Form_Element_Hidden extends Helper_Form_Element_Input{
    		   
            		   
        public function setupOptions(){

            parent::setupOptions();
            $this->addOption( 'type'         , "hidden" );            
            $this->setOption( 'box_renderer' , 'NoBox' );
            
        }
    
    }
