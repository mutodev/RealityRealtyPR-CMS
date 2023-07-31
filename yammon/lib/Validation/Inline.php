<?php

    class Validation_Inline extends Validation{

        protected function setupOptions(){
            parent::setupOptions();
            $this->addOption( "type" );
            $this->addOption( "options", array() );
            $this->addOption( "message" , null );
            $this->addOption( "separator" , "\n" );
            $this->addOption( "trim" , true );
        }

        protected function valid( $inlineValue , $context = null ){

            $type      = $this->getOption( "type" );
            $options   = $this->getOption( "options" );
            $message   = $this->getOption( "message" );
            $separator = $this->getOption( "separator" );
            $trim      = $this->getOption( "trim" );

            //Get Validation Class
            $class = Inflector::classify( $type );
            $class = "Validation_".$class;
            if( !class_exists( $class ) ){
                throw new Exception( "Coundn't load class $class" );
            }

            $validation = new $class($options);

            //Set default message
            if (is_null($message)) {
                $this->setOption("message", $validation->getOption("message"));
            }

            $values = explode($separator, $inlineValue);

            foreach ($values as $value) {

                if ($trim) {
                    $value = trim($value);
                }

                if (!$validation->validate($value, $context)) {
                    return false;
                }
            }

            return true;
        }
    }
