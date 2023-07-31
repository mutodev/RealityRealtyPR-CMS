<?php

    class Helper_Form_Renderer_Error_Inline extends Helper_Form_Renderer_Error{
        
        public function render( $element , $options ){
            
            if( !($element instanceof Helper_Form_Element_Valued ) )
                return "";
                        
            if( !$element->hasError() )
                return '';
            
            $text = $element->getError();
            if( $text == '' ) 
                return '';

            return "<div class='ym-form-error'>$text</div>";
        }

    }
