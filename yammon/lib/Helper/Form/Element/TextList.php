<?php

    class Helper_Form_Element_TextList extends Helper_Form_Element_Multiple
    {
    		            		        
        public function construct(){
            parent::construct();             
            
            $Javascript = helper('Javascript');
            $Javascript->add("/yammon/public/widget/widget.js");
            $Javascript->add("/yammon/public/widget/widget-textlist.js");            
            $Javascript->add("/yammon/public/form/js/TextboxList/GrowingInput.js");            
            $Javascript->add("/yammon/public/form/js/TextboxList/TextboxList.js");
            $Javascript->add("/yammon/public/form/js/TextboxList/TextboxList.Autocomplete.js");            

            $Css       = helper('Css');            
            $Css->add("/yammon/public/form/js/TextboxList/TextboxList.css");
            $Css->add("/yammon/public/form/js/TextboxList/TextboxList.Autocomplete.css");
            
            Event::connect( 'form.handle' , array( $this , "handle" ) );            
            
        }
                	     
        public function handle(){
        
            if( !isset( $_SERVER['HTTP_X_YAMMON_REQUEST']) )
                return;
            
            if( $_SERVER['HTTP_X_YAMMON_REQUEST'] != 'HELPER_FORM_ELEMENT_TEXTLIST' )
                return;
                        
             $Form          = $this->getForm();
             @$search       = $_POST['search'];
             @$element_name = $_POST['element'];
            
             //Check that its my request
             if( $element_name != $this->getFullName() ){
                return;
             }
                                                
             //Return Results
             $json = $this->getJSON( $search );
             header('Content-Type: application/json; charset=utf-8');
             echo json_encode( $json );
             exit();           
           
        
        }
        	     
        public function setupOptions( ){
            parent::setupOptions();
            $this->addOption( "remote"      , true );
            $this->addOption( "min_length"  , 2 );
            $this->addOption( "respond"     , true );                       
            $this->addOption( "parameters"  , null );            
        }
        	  
        private function getJSON( $search = null ){
        
            $possibles  = $this->getPossibleValues( $search );
            $values     = array();
            foreach( $possibles as $k => $v ){
                $values[] = array( $k , $v , $v , $v );                
            }
            return $values;
            
        }

		public function renderStart( )
		{
		
		}
                	  
		public function renderBody( ){
		   
            $Html    = helper('html');		   

            //Prepare the classes that this element will have
            $this->addClass("ym-form-text");

            //Get the options of the form element
            $value      = $this->getValue( );           
            $domid      = $this->getDomId( );
            $domname    = $this->getDomName();
            $password   = $this->getOption( "password" );
            $attributes = $this->getOption( "attributes" );
            $classes    = $this->getClasses();
            $style      = $this->getOption( "style" );
            $size       = $this->getOption( "size" );
            $remote     = $this->getOption( "remote" );
                        
            //Translate value to comma separated list
            $defaults = array();
            if( $value ){

                $value      = (array)$value;
                $source     = $this->getSource();
                $source_key = $this->getSourceKey( $source );
                
                //Construct Query
                $from       = $source->getExpressionOwner("from");
                $q          = new Doctrine_Query();
                $q->from( $from );
                $q->andWhereIn( $source_key , $value );
                
                //Get Records
                $records = $q->execute();
                
                //Transform value
                foreach( $records as $record ){
                    list( $key , $label ) = $this->getPossible( $source , $record  );
                    $defaults[ $key ] = $label;
                }

            }            
                        
            //Prepare the content
            $attributes["id"]                = $domid;
            $attributes["name"]              = $domname;
            $attributes["class"]             = $classes;
            $attributes["style"]             = $style;
            $attributes["type"]              = $password ? "password" : "text";

            //Set Widget Properties
            $remote                = !!$this->getOption('remote');
            $min_length            = $this->getOption('min_length');
            $source_validate       = !!$this->getOption('source_validate');
            $url                   = url( ".".qs() );
            $parameters            = (array)$this->getOption('parameters');
            $parameters['element'] = $this->getFullName();

            $conf = array(
                "plugins" => array(
                    "autocomplete" => array(
                        "queryRemote"     => $remote  ,                                            
                        "minLength"       => $min_length ,
                        "onlyFromValues"  => $source_validate ,
                        "remote"          => array(
                            "url"         => $url , 
                            "extraParams" => $parameters ,
                        )
                    )
                ),
                "values"   => $remote ? array() : $this->getJSON() ,
                "defaults" => $defaults 
            );
                                      
            $attributes["widget"]           = 'TextList';                
            $attributes["widget-textlist"]  = json_encode( $conf );

            //Write the input
            $content   = array();
            $content[] = $Html->tag( "input" , $attributes  );
                        
            $content = implode("\n" , $content );

            //Do the Actual Rendering
            return $content;
            
        }   

        public function renderEnd()
        {
        
        }

        public function getUnfilteredValue(){
        
            $values = parent::getUnfilteredValue();
                        
            if( $values === null || $values == '' )                 
                $values = null;
            elseif( !is_array( $values ) )
                $values = explode( "," , $values );

            return $values;
            
        }
    
    }
