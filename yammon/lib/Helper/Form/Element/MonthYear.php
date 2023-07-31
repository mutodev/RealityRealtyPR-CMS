<?php

    class Helper_Form_Element_MonthYear extends Helper_Form_Element_DateTime{
    		     	
        public function setupOptions(){
           parent::setupOptions(); 
           $this->setOption("format"     , "%M/%y" );
           $this->setOption("year_start" , "-0");           
           $this->setOption("year_end"   , "+10");                      
        }
    		     	        
    }

