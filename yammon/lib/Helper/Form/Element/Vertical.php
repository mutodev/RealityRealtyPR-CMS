<?php

    class Helper_Form_Element_Vertical extends Helper_Form_Element_Container{
    		    	        	            
        public function setupOptions(){
            parent::setupOptions();
            $this->setOption( 'layout_renderer' , 'vertical' );
            $this->setOption( 'box_renderer'    , array(
                'type'      => '1Column' ,
                'highlight' => false ,
                'margin'    => false ,
                'padding'   => false ,
                'border'    => false ,
            ));            
            $this->setOption( 'collect_errors'  , false );  
        }
        
    }
