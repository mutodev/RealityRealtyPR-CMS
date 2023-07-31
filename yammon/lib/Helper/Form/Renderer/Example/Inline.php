<?php

    class Helper_Form_Renderer_Example_Inline extends Helper_Form_Renderer_Example{
         
        public function render( $element , $options ){

            $text    = $element->getExample();
            if( $text == '' ) return '';
            return "<div class='ym-form-example'>$text</div>";
        }

    }
