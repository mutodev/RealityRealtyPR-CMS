<?php

    class Helper_Form_Renderer_Box_3Column extends Helper_Form_Renderer_Box{
        
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

                    $Html->open( "div" , array('class' => 'ym-form-box-column1' ) );
                        $Html->open( "div" , array('class' => 'ym-form-box-column1-inner' ) );                    
                            $Html->text( $element->renderLabel() );
                        $Html->close('div');
                    $Html->close('div');                        

                    $Html->open( "div" , array('class' => 'ym-form-box-column2' ) );
                        $Html->open( "div" , array('class' => 'ym-form-box-column2-inner' ) );                    
                            $Html->text( $element->render() );                        
                            $Html->text( $element->renderExample() );
                            $Html->text( $element->renderError() );                            
                        $Html->close('div');
                    $Html->close('div');  
                    
                    $Html->open( "div" , array('class' => 'ym-form-box-column3' ) );
                        $Html->open( "div" , array('class' => 'ym-form-box-column3-inner' ) );                    
                            $Html->text( $element->renderDescription() );
                        $Html->close('div');
                    $Html->close('div');                      

                    $Html->open( "div" , array('class' => 'ym-form-clear'));
                    $Html->close("div");
                    
                $Html->close( "div" );
            $Html->close( "div" );                        
            
            return $Html->get();
     
        }

    }
