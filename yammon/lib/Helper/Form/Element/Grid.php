<?php

    class Helper_Form_Element_Grid extends Helper_Form_Element_Container{
    		    	        	            
        public function setupOptions(){
            parent::setupOptions();  

            $this->addOption( 'columns'         , '2' );
            $this->addOption( 'rows'            , null );
            $this->addOption( 'align'           , null );
            $this->addOption( 'valign'          , null );            

            $this->setOption( 'layout_renderer'   ,'grid' );
            $this->setOption( 'box_renderer'      , array(
                'highlight' => true ,
            ));
            $this->setOption( 'collect_errors'    , true );              
            $this->setOption( "default_renderers" , array(
                'box_renderer' => array(
                    'type'      => '1Column' ,                
                    'margin'    => false     ,
                    'padding'   => false     ,
                    'border'    => false     ,  
                    'highlight' => false     ,
                ),
                'label_renderer' => array(
                    'small' => true ,
                )
            ));
            
        }
        	    
        public function render( ){
            
            list( $type , $options ) = $this->getRendererOptions( 'Layout' , array() );
            
            $options['columns'] = $this->getOption('columns' , @$options['columns'] );
            $options['rows']    = $this->getOption('rows'    , @$options['rows']    );
            $options['align']   = $this->getOption('align'   , @$options['align']   );
            $options['valign']  = $this->getOption('valign'  , @$options['valign']  );            
            
            return Helper_Form_Renderer_Layout::factory( $type )->render( $this , $options );
        }
        	    
    }
