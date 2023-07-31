<?php

    class Helper_Form_Element_Buttons_Wizard extends Helper_Form_Element_Buttons{
    		            	    	    
        public function setupOptions(){
            parent::setupOptions();
            $this->addOption( "buttons"      , array(
                "previous" => array() ,            
                "next"     => array() ,   
                "save"     => array() ,                   
                "cancel"   => array() ,                
            ));   
        }
                	    	        	    	
        public function getButtons(){
        
            $buttons = parent::getButtons();
                                                            
            //Find the wizard
            $form   = $this->getForm();
            $wizard = null;
            foreach( $form->getElements() as $element ){
                if( $element instanceof Helper_Form_Element_Wizard ){
                    $wizard = $element;
                    break;
                }
            }
            
            //If there are no wizards don't return any buttons
            if( !$wizard )
                return array();

            //Get wizard information
            $step       = $wizard->getStep();
            $step_count = $wizard->getStepCount();
            $edit       = $wizard->isEdit();            
                        
            //Disable Previous Button
            if( $step <= 0 && isset( $buttons['previous'] ) ){
                $buttons['previous']->addClass('disabled');
                $buttons['previous']->addAttribute('disabled' , 'disabled');                
            }                

            //Hide Next Button
            if( $step >= $step_count - 1 && isset( $buttons['next'] ) ){            
                $buttons['next']->addClass('disabled');
                $buttons['next']->addAttribute('disabled' , 'disabled');            
                $buttons['next']->addStyle('display' , 'none');                            
            }
                                
            //Hide Save Button
            if( ( !$edit && $step < $step_count - 1 ) && isset( $buttons['save'] ) ){            
                $buttons['save']->addClass('disabled');
                $buttons['save']->addAttribute('disabled' , 'disabled');  
                $buttons['save']->addStyle('display' , 'none');                            
            }
            
            return $buttons;
        
        }


    }
