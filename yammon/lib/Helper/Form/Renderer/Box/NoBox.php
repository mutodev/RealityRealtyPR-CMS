<?php

    class Helper_Form_Renderer_Box_NoBox extends Helper_Form_Renderer_Box{


        public function render( $element , $options ){

            //Dependencies
            if ($dependencies = $this->getDependencies($element, $options)) {
                $element->addAttribute('ym-form-dependencies', $dependencies);
            }

            //Visible
            if (!($visible = $element->isVisible())) {
                $styles = $element->getStyles(true);
                $styles['display'] = 'none';
                $element->setOption('style', $styles);
            }

            return $element->render();
        }

    }
