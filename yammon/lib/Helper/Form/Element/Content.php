<?php

    class Helper_Form_Element_Content extends Helper_Form_Element{

        public function setupOptions(){
            parent::setupOptions();
            $this->addOption('content' , null );
            $this->addOption('include' , null );
            $this->setOption("box_renderer" , 'NoBox' );
        }

		public function render( ){

 		    //Get the Content
		    $content = $this->getOption("content");
		    $content = t($content);
		    $include = $this->getOption("include" );
		    if( $include ){

    		    $include = $this->getOption("include");
	    	    ob_start();
                require_once( $include );
                $content .= $include;

            }

            //Render
            $this->addClass('ym-form-content');
            $this->addAttribute( 'id'          , $this->getDomId() );
            $attributes = $this->getAttributes();

            $html   = array();
            $html[] = "<div $attributes>";
            $html[] = $content;
            $html[] = "</div>";
            return implode("\n" , $html);


        }

        public function getTranslationStrings(){

            $strings = parent::getTranslationStrings();
            $string  = $this->getOption('content');
            if( $string ) $strings[] = $string;

            return $strings;

        }

    }
