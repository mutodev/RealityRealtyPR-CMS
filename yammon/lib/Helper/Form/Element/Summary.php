<?php

    class Helper_Form_Element_Summary extends Helper_Form_Element{

        public function setupOptions(){
            parent::setupOptions();
            $this->addOption( "message"      , null );
            $this->setOption( "box_renderer" , "NoBox" );
        }

        public function render( ){

            $output  = array();
            $parent  = $this->getParent();
            $errors  = $parent->getErrors();
            $message = t( $this->getOption( "message" , t("There was an error with your submission" ) ) );

            $Html = new Html();

            if( !empty( $errors) ){

                $Html->open('div' , array('class' => 'ym-form-summary alert alert-danger') );
                    $Html->text( $message );
                    $Html->open('ul');
                        foreach( $errors as $name => $error ){

                            $element    = $parent->getElement( $name );
                            $element_id = $element ? $element->getDomId( ) : '';

                            $Html->open('li');
                                $Html->open('a' , array('href' => "#$element_id" ) );
                                    $Html->text( $error );
                                $Html->close('a');
                            $Html->close('li');

                        }
                    $Html->close('ul');
                $Html->close('div' , array('ym-form-summary') );

            }

	        return $Html->get();

        }

        public function getTranslationStrings(){

            $strings = parent::getTranslationStrings();
            $message    = $this->getOption('message');
            if( $message ) $strings[] = $message;

            return $strings;

        }

    }

