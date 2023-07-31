<?php

    class Helper_Form_Element_Checkbox extends Helper_Form_Element_Valued{

        public function setupOptions(){
            parent::setupOptions();
            $this->addOption("disabled" , null );
            $this->addOption("readonly" , null );
            $this->addOption("text"     , null );
            $this->addOption("true"     , "1" );
            $this->addOption("false");
            $this->setOption("false"    , "0" );
            $this->setOption("default"  , "0" );
        }

		public function render( ){

            $id      = $this->getDomId();
            $text    = $this->getOption('text');
            $true    = $this->getOption('true');
            $false   = $this->getOption('false');
            $default = $this->getOption('default');
            $value   = $this->getValue();
            $value   = $value === null ? $default : $value;
            $checked = $value ? 'checked' : null;
            $text    = t( $text );



            //Render the element
            $this->addAttribute( 'id'         , $id );
            $this->addAttribute( 'name'       , $this->getDomName() );
            $this->addAttribute( 'value'      , $true );
            $this->addAttribute( 'type'       , 'checkbox' );
            $this->addAttribute( 'checked'    , $checked );
            $this->addAttribute( 'disabled'   , $this->getOption('disabled') );
            $this->addAttribute( 'readonly'   , $this->getOption('readonly') );
            $this->addClass('ym-form-checkbox');
            $attributes = $this->getAttributes( false );

            $content   = array();
            $content[] = "<input type='hidden' name='".$this->getDomName()."' value='$false' />";
            $content[] = "<input $attributes />";
            if( $text ) $content[] = "<label for='$id'> $text </label>";

            return implode( "\n" , $content );

        }

        public function getValue(){

            $true   = $this->getOption('true');
            $false  = $this->getOption('false');
            $value = parent::getValue();

            if( $value === null )
                return null;
            elseif( $value == $true )
                return $true;
            else
                return $false;

        }

        public function getTranslationStrings(){

            $strings     = parent::getTranslationStrings();
            $string      = $this->getOption('text');
            if( $string ) $strings[] = $string;

            return $strings;

        }

    }
