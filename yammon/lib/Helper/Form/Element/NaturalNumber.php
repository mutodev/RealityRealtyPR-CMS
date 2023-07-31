<?php

    class Helper_Form_Element_NaturalNumber extends Helper_Form_Element_Number{
    		      	
		public function construct(){
		    parent::construct();
            $this->addValidation('Natural');
		}    		      	
    		      	
        public function getValue(){
            $value = parent::getValue();

            if( $value == null ) 
                return null;
    
            $fvalue = trim(str_replace(',', '', $value ));
            if( !is_numeric( $fvalue ) ) 
                return $value;

            return floor($fvalue);

        }
    		      	
        public function getFormattedValue(){
            $value = $this->getValue();

            if( $value === null ) 
                return null;

            $fvalue = str_replace(',', '', $value );
            if( !is_numeric( $fvalue ) ) 
                return $value;

            return number_format( floor($fvalue) , 0 );
            
        }
                	
        	
    }

