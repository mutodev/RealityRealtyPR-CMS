<?php

    class Helper_Form_Renderer_Box_1Column extends Helper_Form_Renderer_Box{

      public function render( $element , $options ){

            $classes      = $this->getClasses( $element , $options );
            $style        = $this->getStyle( $element , $options );
            $dependencies = $this->getDependencies( $element , $options );

            $attributes   = array();
            $attributes['id']    = "box_".$element->getDomId();
            $attributes['class'] = $classes ? $classes : null;
            $attributes['style'] = $style ? $style : null;
            $attributes['ym-form-dependencies'] = $dependencies ? $dependencies : null;

            $Html = new Html();
            $Html->open( "div" , $attributes );
                $Html->open( "div" , array('class' => 'ym-form-box-inner' ) );
                    $Html->text( $element->renderLabel() );
                    $Html->text( $element->renderDescription() );
                    $Html->text( $element->render() );
                    $Html->text( $element->renderExample() );
                    $Html->text( $element->renderError() );
                $Html->close( "div" );
            $Html->close( "div" );

            return $Html->get();

        }

    }
