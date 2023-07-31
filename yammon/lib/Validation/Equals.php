<?php

    class Validation_Equals extends Validation{

        protected function setupOptions(){
            parent::setupOptions();
            $this->setOption( "message" , "%{label} is invalid" );
            $this->addOption( "element" , null );
        }

        protected function valid( $value , $context = null ){

            //Get the element name
            $element_name = $this->getOption('element');

            //Validate Parameters
            if( !$element_name ) return false;
            if( !$context )      return false;

            //Get the element
            $element = $context->getElement( $element_name );

            //If we didn't find it return false
            if( !$element )      return false;

            //Return
            return $element->getValue() == $value;

        }

    }