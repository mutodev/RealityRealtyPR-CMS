<?php

    abstract class Helper_Form_Element_Multiple extends Helper_Form_Element_Sourced{
                	
        public function getValue(){
              
            $validate = $this->getOption("source_validate");
            $value    = (array)Helper_Form_Element_Valued::getValue();

            if( $validate ) {
                $options = $this->getPossibleValues( null , false , false );
                foreach( $value as $k => $v ){
                    if( @!array_key_exists( $v , $options ) )
                        unset( $value[$k] );
                }
                $value = array_values( $value );
            }

            return $value;
                        
        }
                
        public function getDefaultValue(){
            return (array) parent::getDefaultValue();
        }                	
                	
    }
