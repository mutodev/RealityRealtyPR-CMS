<?php

    class Helper_Form_Renderer_Layout_Horizontal extends Helper_Form_Renderer_Layout_Grid{
        
        public function render( $element , $options ){

            $elements = $element->getElements();  

            //Calculate the number of columns 
            $columns = count( $elements );
    
            $hidden_elements = array(); 
            foreach( $elements as $el )
                if( $el instanceOf Helper_Form_Element_Hidden ){ 
                    $columns--; 
                } 
    
            //Render
            $options['columns'] = max( $columns , 1 ); 
            $options['rows'] = null; 
            return parent::render( $element , $options ); 
        }
        
    }
