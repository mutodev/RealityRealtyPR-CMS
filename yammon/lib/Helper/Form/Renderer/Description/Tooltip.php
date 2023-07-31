<?php

    class Helper_Form_Renderer_Description_Tooltip extends Helper_Form_Renderer_Description{
                
        public function render( $element , $options ){

            $text    = $element->getDescription();
            if( $text == '' ) return '';
            return "<div class='ym-form-description ym-form-description-tooltip'><span class='ym-form-description-tooltip-stem-border'></span><span class='ym-form-description-tooltip-stem'></span>$text</div>";
        }

    }
