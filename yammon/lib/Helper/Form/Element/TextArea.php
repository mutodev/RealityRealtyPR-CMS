<?php

    class Helper_Form_Element_TextArea extends Helper_Form_Element_Valued{

        public function setupOptions(){
            parent::setupOptions();
            $this->addOption( 'attributes'  , array() );
            $this->addOption( 'classes'     , null    );
            $this->addOption( 'style'       , null    );
            $this->addOption( 'type'        , 'text'  );
            $this->addOption( 'rows'        , 5       );
            $this->addOption( 'placeholder' , null      );
        }

		public function render( ){

            $Html    = helper('html');

            //Get the options of the form element
            $value       = $this->getValue( );
            $domid       = $this->getDomId( );
            $domname     = $this->getDomName();
            $password    = $this->getOption( "password" );
            $attributes  = $this->getOption( "attributes" );
            $classes     = $this->getOption( "classes", $this->getOption( "class") );
            $style       = $this->getOption( "style" );
            $rows        = $this->getOption( "rows" );
            $placeholder = t($this->getOption( "placeholder" ));

            //Add Classes
            $classes   = (array) $classes;
            $classes[] = "ym-form-text";

            //Prepare the content
            $attributes                = (array)$attributes;
            $attributes["id"]          = $domid;
            $attributes["name"]        = $domname;
            $attributes["class"]       = $classes;
            $attributes["style"]       = $style;
            $attributes["rows"]        = $rows;
            $attributes["placeholder"] = $placeholder;

            //Set the content options for rendering
            $content = $Html->startTag( "textarea" , $attributes );
            $content = $content.$value;
            $content = $content.$Html->endTag("textarea");

            //Do the Actual Rendering
            return $content;

        }

        public function getTranslationStrings()
        {

            $strings     = parent::getTranslationStrings();
            $string      = $this->getOption('placeholder');
            if( $string ) $strings[] = $string;

            return $strings;

        }

    }
