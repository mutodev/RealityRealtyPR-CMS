<?php

    class Helper_Form_Element_SelectCheckbox extends Helper_Form_Element_CheckBoxes{
    		            
        public function setupOptions(){
            parent::setupOptions();            
        }
    
        public function build()
        {
            helper('Css')->add('/yammon/public/widget/selectcheckbox/selectcheckbox.css');
            helper('Javascript')->add('/yammon/public/widget/selectcheckbox/selectcheckbox.js');            
            $this->addAttribute('widget' , 'SelectCheckbox' );    
        }
        
    }
