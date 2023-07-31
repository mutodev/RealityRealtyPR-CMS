<?php

    class Helper_Form_Element_Header extends Helper_Form_Element{
    	      	            
        public function setupOptions(){
            parent::setupOptions();
            $this->addOption("image");
            $this->setOption("box_renderer" , 'NoBox' );            
        }
    	    	    	 	    	    	    	    	
		function render( $opts = array() ){
		
		    $image       = $this->getOption("image");
		    $label       = $this->getLabel();
		    $description = $this->getDescription();
		    $label       = t($label);

		    $this->addClass('ym-form-header');
		    $attributes = $this->getAttributes();
		    
		    $content   = array();
		    $content[] = "<div $attributes>";
            if( $image )
                $content[] = "<img src='$image' />";

            if( $label )    		    		    
                $content[] = "<h3>$label</h3>";

            if( $description )    		    		    
                $content[] = "<p>$description</p>";
		    $content[] = "</div>";
		
            return implode("\n" , $content );
		
		}    
    }

