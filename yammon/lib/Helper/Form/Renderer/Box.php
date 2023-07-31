<?php

    abstract class Helper_Form_Renderer_Box extends Helper_Form_Renderer{

        static public function factory( $type ){
            $type = $type ? $type : '1Column';
            return Helper_Form_Renderer::factory( 'Box' , $type );
        }
                
        protected function getDependencies( $element , $options ){
        
            $return = array();         
            $dependencies = $element->getDependencies();
            foreach( $dependencies as $name => $options ){
                $subject = $element->getRelative( $name );
                if( !$subject ){
                    continue;
                }
                $return[] = $subject->getDomId();
            }
            
            if( !empty( $return ) )
                return $element->getFullName().":".implode( "," , $return );
            
            return null;
            
        }
        
       
                
        protected function getClasses( $element , $options ){
        
            $border             = isset( $options['border'] )    ? $options['border']       : true;
            $margin             = isset( $options['margin'] )    ? $options['margin']       : true;
            $padding            = isset( $options['padding'] )   ? $options['padding']      : true;
            $highlight          = isset( $options['highlight'] ) ? $options['highlight']    : false;
            $uclasses           = isset( $options['class'] )     ? (array)$options['class'] : array();
            $has_error          = ($element instanceof Helper_Form_Element_Valued ) ? $element->hasError() : false;

            //Construct the classes
            $classes   = array();
            $classes[] = "ym-form-box";
            $classes[] = "form-group";
            $classes[] = "ym-form-box-".strtolower( substr( get_class( $this ) , strlen('Helper_Form_Renderer_Box_') ) );
                        
            if( $has_error )
                $classes[] = "ym-form-box-error";
            
            if( $border === true )
                $classes[] = "ym-form-box-border";  
            elseif( $border === false )
                $classes[] = "ym-form-box-no-border";
                
            if( $margin === true )
                $classes[] = "ym-form-box-margin";
            elseif( $margin === false )
                $classes[] = "ym-form-box-no-margin";
                
            if( $padding === true )
                $classes[] = "ym-form-box-padding";
            elseif( $padding === false )
                $classes[] = "ym-form-box-no-padding";
                
            if( $highlight )            
                $classes[] = "ym-form-box-highlight";
            else                
                $classes[] = "ym-form-box-no-highlight";
                
            foreach( $uclasses as $uclass ){
                $classes[] = $uclass;
            }
                
            return implode( " " , $classes );
        
        }

        protected function getStyle( $element , $options ){

        
            $border    = isset( $options['border'] )  ? $options['border']  : true;
            $margin    = isset( $options['margin'] )  ? $options['margin']  : true;
            $padding   = isset( $options['padding'] ) ? $options['padding'] : true;
            $stylei    = isset( $options['style'] )   ? $options['style'] : null;
            $visible   = $element->isVisible();
            
            //Construct the classes
            $style     = array();
            
            if( !empty( $stylei ) )
                $style[] = $stylei;
            
            if( !is_null( $border ) && !is_bool( $border ) )
                $style[] = "border: $border;";

            if( !is_null( $margin ) && !is_bool( $margin ) )
                $style[] = "margin: $margin;";

            if( !is_null( $padding ) && !is_bool( $padding ) )
                $style[] = "padding: $padding;";
        
            if( !$visible ){
                $style[] = "display:none;";
            }
            
            return implode( "" , $style );        
        
        }
                
    }
