<?php

    class Validation_NumericMin extends Validation{

        protected function setupOptions()
        {
            parent::setupOptions();
            $this->setOption( "message" , "%{label} must not be less than %{minimum}" );
            $this->addOption( "minimum" , 1 );
        }

        public function getMessage( $arguments = array() )
        {
            $minimum              = $this->getOption("minimum");
            $arguments['minimum'] = $minimum;

            return parent::getMessage($arguments);
        }

        protected function valid( $value , $context = null )
        {
            $minimum = $this->getOption('minimum');

            return $minimum <= $value;
        }

    }