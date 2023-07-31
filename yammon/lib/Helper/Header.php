<?php

    class Helper_Header extends Helper{
    	    	    	 	    			
		public function construct(){
		   $Css = helper('css');
           $Css->add( "/yammon/public/header/css/header.css" );		
		}
    	    
        public function setupOptions(){
            $this->addOption( "image" , "" );
            $this->addOption( "title" , "" );
            $this->addOption( "description" , "" );
        }
    	    
        function setOption( $key , $value ){
            parent::setOption( $key , $value );  
        }
    	    
		function render( $options = array() ){
		
            if( $options ){
    		    $this->setOptions( $options );
		    }

		    $Html = helper('Html');
		
		    $image       = $this->getOption('image');
		    $title       = $this->getOption('title');
		    $description = $this->getOption('description');
		    
		    $Html->clear();
		    $Html->open("div" , array( "class" => "page_header") );
		    
		        if( $image )
        		    $Html->open("img" , array( "src"   => $image ) );

		        if( $title )    		    		    
    	    	    $Html->open("h1"  , null , $title );

		        if( $description ){
	        	    $Html->open("p");
        	    	    $Html->text( $description );
	        	    $Html->close();
                }
	    	    
    	    $Html->close();

            $content = $Html->render();		    
            return $content;
		
		}    
    }

