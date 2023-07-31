<?php

    class Helper_Form_Element_Email extends Helper_Form_Element_Input{
    		    
        public function setupOptions(){
            parent::setupOptions();
            $this->setOption('type' , 'email' );       
        }

		public function construct(){
            $this->addValidation('email');
            $this->addClass('ym-form-text');
            $this->addClass('ym-form-email');
            $this->addClass('form-control');
		}
        	    		          	    		    		    		    
    }
