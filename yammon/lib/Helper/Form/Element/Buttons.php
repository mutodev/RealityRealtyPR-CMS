<?php

    class Helper_Form_Element_Buttons extends Helper_Form_Element_Container{
    		    	        	            
        public function setupOptions(){
            parent::setupOptions();  
            $this->addOption( "buttons"      , array(
                "save"   => array() ,
                "cancel" => array() ,                
            ));            
            $this->setOption( "box_renderer" , "NoBox" );
        }
        	    	    
        protected function getButtons(){
        
		    //Get the Buttons
		    $buttons = (array)$this->getOption('buttons');
		    $objects = array();
		    foreach( $buttons as $name => $options ){
		    
		        $type = isset( $options['type'] ) ? $options['type'] : null;

                if( $type == null )
                    $class = 'Helper_Form_Element_Button_'.ucfirst( $name );

                if( empty( $class ) || !class_exists( $class ) )
                    $class = 'Helper_Form_Element_Button';
                
		        $button = new $class( $name , $options );
                $objects[ $name ] = $button;
		    }
		    
		    return $objects;
        
        }
        	    	    
		public function render( ){
		   		   		   
            //Render the Buttons
            $this->addClass('ym-form-buttons');
            $attributes = $this->getAttributes();
            
            $content   = array();
            $content[] = "<div $attributes>";
            foreach( $this->getButtons() as $object ){
                $content[] = $object->render();
            }
            $content[] = "</div>";
            
            return implode("\n" ,  $content );
            
        }        
    
    }
