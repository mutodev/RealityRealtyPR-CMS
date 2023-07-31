<?php

    abstract class Helper_Form_Element_Button_Repeat extends Helper_Form_Element_Button{
    		    		
        public function setupOptions(){
            parent::setupOptions();
            $this->addOption("repeat_element" , false );
        }                   		         	       	
    
        public function render(){
            $repeat_element_name = $this->getOption('repeat_element');
            $repeat_element      = $this->getRelative( $repeat_element_name );
            if( $repeat_element )
                $this->addAttribute('repeat-element' , $repeat_element->getDomId() );
            return parent::render();
        }
    
    }
