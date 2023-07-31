<?php

    class Helper_Form_Renderer_Description_Boxed extends Helper_Form_Renderer_Description{
                
        public function render( $element , $options ){

            $text    = $element->getDescription();
            if( $text == '' ) return '';
            return "<div class='ym-form-description ym-form-description-boxed'>$text</div>";
        }

    }
