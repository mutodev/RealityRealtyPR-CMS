<?php

    class Helper_Table_Decorator_Email extends Helper_Table_Decorator{

        public function apply( $value ){
            
            $validator = new Validation_Email();
            if( $validator->validate( $value ) )
                return "<a href='mailto:$value'>$value</a>";
            else
                return $value;
                
        }
    
    }
