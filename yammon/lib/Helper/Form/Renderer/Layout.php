<?php

    abstract class Helper_Form_Renderer_Layout extends Helper_Form_Renderer{
    
        static public function factory( $type ){
            $type = $type ? ucfirst( $type ) : 'Vertical';
            return Helper_Form_Renderer::factory( 'Layout' , $type );        
        }  
        
        protected function getClasses( $element , $options ){
        
            //Construct the classes
            $classes   = isset( $options['class'] ) ? (array) $options['class'] : array();
            $classes[] = "ym-form-layout";
            $classes[] = "ym-form-layout-".strtolower( substr( get_class( $this ) , strlen('Helper_Form_Renderer_Layout_') ) );                        
            return implode( " " , $classes );
        
        }

        protected function getStyle( $element , $options ){
        
            $style   = isset( $options['style'] ) ? $options['style']  : "";      
            return $style;
        
        }        
        
    }
