<?php

    class Helper_Form_Element_Url extends Helper_Form_Element_Input{
    		            
        public function setupOptions(){
            parent::setupOptions();
            $this->setOption('type' , 'url' );
            $this->addValidation('url');
            $this->addClass('ym-form-text');
            $this->addClass('ym-form-url');   
        }
       	    		
       	    		          	    		    		    		    
    }
