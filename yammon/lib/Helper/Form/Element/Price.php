<?php

    class Helper_Form_Element_Price extends Helper_Form_Element_Text{

        public function setupOptions(){
            parent::setupOptions();

            $this->addOption( 'cents' , false );
            $this->setOption( 'size'  , 11 );
        }
        	        	
        public function render(){
            		   
            //Get the options of the form element
            $value       = $this->getValue( );
            $domid       = $this->getDomId( );
            $domname     = $this->getDomName();
            $cents       = $this->getOption('cents');
                        
            //Prepare the value            
            $dollars_value = '';
            $cents_value   = '';
            if( $value !== null ){
                $value         = number_format( $value , 2 );
                $value         = explode( "." , $value );
                $dollars_value = @$value[0];
                $cents_value   = str_pad( @$value[1] , 2 , "0" , STR_PAD_LEFT );
            }

            //Prepare the content
            $this->setupAttributes();
            $this->addAttribute( 'id'          , $domid."_dollars" );
            $this->addAttribute( 'name'        , $domname."[dollars]" );
            $this->addAttribute( 'value'       , $dollars_value );

            $attributes2              = array();
            $attributes2["id"]        = $domid."_cents";
            $attributes2["name"]      = $domname."[cents]";
            $attributes2["class"]     = "ym-form-text";
            $attributes2["value"]     = $cents_value;
            $attributes2["type"]      = 'text';
            $attributes2["size"]      = 2;
            $attributes2["maxlength"] = 2;

            //Set the content options for rendering
            $Html = new Html();            
            $Html->text("<span class='input-group-addon'>$</span>");
            $Html->open("input" , $this->getAttributes(true) , null , true );
            if( $cents ){
                $Html->text(" . ");            
                $Html->open("input" , $attributes2 , null , true );            
            }

            $content   = array();
            $content[] = "<div class=\"input-group\">";
            $content[] = $Html->get();
            $content[] = "</div>";
         
            //Do the Actual Rendering
            return implode('', $content);
            
        }
        	
        public function normalizeValue( $value ){
                        
            if( $value === null || $value === '' )
                return null;
                                
            if( is_array( $value ) ){
                        
                foreach( $value as $k=> $v )
                    if( trim( $v ) == '' )
                        unset( $value[$k] );
            
                if( $value == array() )
                    return null;

                $dollars  = input( $value , 'dollars' , 0 );
                $cents    = str_pad( input( $value , 'cents' , 0 ) , 2 , "0" , STR_PAD_LEFT );
                $value    = $dollars.".".$cents;

            }
                                                    
            $value = trim(preg_replace('/[^0-9.]/', "" , $value ));
            $value = (float)$value;
            return $value;
            
        }
        	
    }

