<?php

    class Validation_NumericMax extends Validation{

        protected function setupOptions()
        {
            parent::setupOptions();
            $this->setOption( "message" , "%{label} must not be more than %{maximum}" );
            $this->addOption( "maximum" , 0 );
        }

        public function getMessage( $arguments = array() )
        {
            $maximum              = $this->getOption("maximum");
            $arguments['maximum'] = $maximum;

            return parent::getMessage($arguments);
        }

        protected function valid( $value , $context = null )
        {
            $maximum = $this->getOption('maximum');

            return $maximum >= $value;
        }

    }