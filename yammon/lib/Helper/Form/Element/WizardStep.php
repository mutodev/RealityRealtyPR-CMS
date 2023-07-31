<?php

    class Helper_Form_Element_WizardStep extends Helper_Form_Element_Container{
    
        public function setupOptions(){
            parent::setupOptions();
            $this->setOption("collect_errors"   , false );
            $this->setOption("box_renderer"     , 'NoBox' );
        }    
    
    }
