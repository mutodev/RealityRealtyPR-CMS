<?php

    class Helper_Form_Element_Radio extends Helper_Form_Element_Valued{
    		    
        static protected $registry = array();    		    
    		    
    		
        public function __construct( $name , $options = array() , $parent = null ){
                  
            //Save the radio to the registry
            $group = isset( $options['group'] ) ? $options['group'] : 'radios';
            self::$registry[ $group ][ $name ] = $this;
                        
            //Call parent construct
            parent::__construct( $name , $options , $parent );
            
        }
    		
        public function setupOptions(){
            parent::setupOptions();      
            $this->addOption("disabled" , null );
            $this->addOption("readonly" , null );            
            $this->addOption("text"     , null );            
            $this->addOption("group"    , 'radios' );            
            $this->addOption("value"    , null );                        
        }
                	   
        public function construct(){

            //Overwrite the domname
            $domname = $this->getFullName();
            $domname = explode( "." , $domname );
            array_pop( $domname );
            $domname[] = $this->getOption('group');
            $domname   = implode( "." , $domname );
            $this->setOption('domname' , $domname );
            
        }
         
        public function setValue( $value ){
        
            //Save the value to all radios in the registry
            $group = $this->getOption('group');
            foreach( self::$registry[ $group ] as $radio ){
            
                //Remove Cache
                unset( $radio->cache['getValue'] );            
                unset( $radio->cache['getUnfilteredValue'] );

                //Set the value
                $radio->passed_value    = $radio->normalizeValue( $value );
                $radio->has_passed_value = true;

                //Notify change
                $event = new Event( $radio , "form.value.changed" );
                Event::notify( $event );            
            
            }
        
        }
        
        
        public function getDefaultValue(){
                                                                                        
            //Get the first default value
            $group = $this->getOption('group');
            foreach( self::$registry[ $group ] as $radio ){
                $default = $radio->getOption('default');
                if( $default !== null ){            
                    return $default;
                }                    
            }                    
                                        
            return null;                    
        
        }
                         
        public function getDomNamePath(){
            return $this->getOption('group');
        }
                                 
		public function render( ){
		   		   		   
            $name    = $this->getName();		   		   		   
            $id      = $this->getDomId();
            $text    = t($this->getOption('text'));
            $true    = $this->getOption('value' , $name );         
            $value   = $this->getValue();
            $checked = $value == $true ? 'checked' : null;
		   		     		   		     
            //Render the element
            $this->addAttribute( 'id'         , $id );   
            $this->addAttribute( 'name'       , $this->getOption('group') );   
            $this->addAttribute( 'value'      , $true );            
            $this->addAttribute( 'type'       , 'radio' );
            $this->addAttribute( 'checked'    , $checked );               
            $this->addAttribute( 'disabled'   , $this->getOption('disabled') );
            $this->addAttribute( 'readonly'   , $this->getOption('readonly') );
            $this->addClass('ym-form-radio');
            $attributes = $this->getAttributes( false );
                   
            $content   = array();
            $content[] = "<input $attributes />";
            if( $text ) $content[] = "<label for='$id'> $text </label>";
            
            return implode( "\n" , $content );
            
        }        
        
        public function getTranslationStrings(){
        
            $strings = parent::getTranslationStrings();
            $string  = $this->getOption('text');
            if( $string ) $strings[] = $string;
        
            return $strings;
        
        }        
    
    }
