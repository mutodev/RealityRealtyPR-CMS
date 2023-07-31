<?php

    class Helper_Form_Element_Time extends Helper_Form_Element_DateTime{

        public function setupOptions(){
            parent::setupOptions();
            $this->setOption('format' , '%H:%i' );
        }

    }

