<?php

    class Helper_Table_Decorator_LocalDateTime extends Helper_Table_Decorator{

        public function setupOptions(){
            parent::setupOptions();
            $this->addOption('format' , 'long-time' );
        }

        public function apply( $value ){

            if (!$value) {
                return '';
            }

            $format = $this->getOption('format');

            return Util::tdateLocal($format, $value);
        }

    }
