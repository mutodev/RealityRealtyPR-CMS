<?php

    class Helper_Form_Renderer_Label_Inline extends Helper_Form_Renderer_Label{
        
        public function render( $element , $options ){

            $label         = $element->getLabel();
            $required      = ($element instanceof Helper_Form_Element_Valued ) ? $element->isRequired() : false;
            $required_tpl  = isset( $options['required_tpl'] ) ? $options['required_tpl'] : "<span class='ym-form-req-glyph'>*</span>";
            $small         = isset( $options['small'] )        ? (bool)$options['small'] : false;

            $id       = $element->getDomId();
            
            if( $label == '' ) 
                return '';
                
            if( $small )
                $label = "<small>$label</small>";

            if( !$required ){
                return "<label for='$id' class='ym-form-label control-label'>$label</label>";
            }else{
                return "<label for='$id' class='ym-form-label control-label ym-form-label-req'>$label $required_tpl </label>";
            }
     
        }

    }
