<?php

    class Helper_Form_Renderer_Layout_Vertical extends Helper_Form_Renderer_Layout{
    
        public function render( $element , $options ){

            $elements = $element->getElements();
            $classes  = $this->getClasses( $element , $options  );
            $style    = $this->getStyle( $element , $options  );
            
            $output   = array();
            $output[] = "<div class='$classes' style='$style'>";
            foreach( $elements as $element ){
                $output[] = $element->renderBox();
            }
            $output[] = "</div>";
            
            return implode( "\n" , $output );
            
        }

    }
