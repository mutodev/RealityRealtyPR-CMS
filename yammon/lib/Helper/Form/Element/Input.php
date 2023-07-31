<?php

    class Helper_Form_Element_Input extends Helper_Form_Element_Valued{

        public function setupOptions(){
            parent::setupOptions();

            $this->addOption( 'checked'      , null );
            $this->addOption( 'disabled'     , null );
            $this->addOption( 'maxlength'    , null );
            $this->addOption( 'readonly'     , null );
            $this->addOption( 'size'         , null );
            $this->addOption( 'src'          , null );
            $this->addOption( 'type'         , 'text');
            $this->addOption( 'placeholder'  , null );
            $this->addOption( 'autocomplete' , true );
        }

        public function getFormattedValue()
        {
            $value = $this->getValue();

            return is_null($value) ? $this->getDefaultValue() : $value;
        }

        public function setupAttributes()
        {
            //Render the element
            $this->addAttribute( 'id'          , $this->getDomId() );
            $this->addAttribute( 'name'        , $this->getDomName() );
            $this->addAttribute( 'value'       , $this->getFormattedValue() );
            $this->addAttribute( 'type'        , $this->getOption('type') );
            $this->addAttribute( 'checked'     , $this->getOption('checked') );
            $this->addAttribute( 'disabled'    , $this->getOption('disabled') );
            $this->addAttribute( 'maxlength'   , $this->getOption('maxlength') );
            $this->addAttribute( 'readonly'    , $this->getOption('readonly') );
            $this->addAttribute( 'size'        , $this->getOption('size') );
            $this->addAttribute( 'src'         , $this->getOption('src') );
            $this->addAttribute( 'placeholder' , t($this->getOption('placeholder')) );

            if( !$this->getOption('autocomplete') )
                $this->addAttribute( 'autocomplete' , 'off' );
        }

		public function render()
        {
            //Helper
            $Html = helper('Html');

            //Render the element
            $this->setupAttributes();

            $attributes = $this->getAttributes(true);

            $content = $Html->startTag( "input" , $attributes, true );

            return $content;
        }

        public function getErrorLabel()
        {
            if ( parent::getErrorLabel() )
                return parent::getErrorLabel();

            else if ( $this->getOption('placeholder') )
                return t( $this->getOption('placeholder') );

            else
                return '';
        }

        public function getTranslationStrings()
        {

            $strings     = parent::getTranslationStrings();
            $string      = $this->getOption('placeholder');
            if( $string ) $strings[] = $string;

            return $strings;

        }

    }
