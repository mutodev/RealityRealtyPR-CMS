<?php

    class Helper_Form_Element_Year extends Helper_Form_Element_NaturalNumber{
    
        public function construct(){
            parent::construct();
            $this->addValidation('Natural');
            $this->addValidation('Length' , array( 'length' => 4) );
        }
    
        public function setupOptions(){
            parent::setupOptions();
            $this->setOption( 'size'      , "4" );
            $this->setOption( 'maxlength' , "4" );            
        }
    
    }
