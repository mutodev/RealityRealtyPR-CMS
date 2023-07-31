<?php

    class Helper_Form_Element_Horizontal extends Helper_Form_Element_Grid{
    		    	        	            
        public function setupOptions(){
            parent::setupOptions();
            $this->setOption( 'layout_renderer' , 'horizontal' );            
            $this->setOption( 'collect_errors'  , true );  
        }
        
    }
