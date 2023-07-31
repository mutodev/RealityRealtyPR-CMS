<?php
	
    class Console_UI_Menu{
        
        public $options = array();
        public $title   = "";
        public $prompt  = "";
        
        public function __construct( $options , $title = '' , $prompt = '' , $invalid = '' ){
            $this->options = (array) $options;
            $this->title   = $title;
            $this->prompt  = !empty( $prompt )  ? $prompt  : "%BChoose One: %n";
            $this->invalid = !empty( $invalid ) ? $invalid : "%RInvalid Selection, Please try again%n";
        }
        
        public function show(){

            $value = null;
            $keys  = array_keys( $this->options );  

            do{

                Console::writeLine();
                if( $this->title ){
                    Console::writeLine( $this->title );
                    Console::writeLine();
                }                    
        
                $i = 1;
                foreach( $this->options as $option ){
                    Console::write( "    " );
                    Console::write( "%B". ($i++) . ".%n" );
                    Console::write( " " );
                    Console::write( $option );                    
                    Console::write( "\n" );
                }
            
                //Read Answer from console
                Console::writeLine();                     
                $value = Console::read( $this->prompt );
                Console::writeLine();
                
                //Validate value
                if( $value == '' )
                    $value == null;   
                elseif( !is_numeric( $value ) )
                    $value = null;
                elseif( $value <= 0 || $value > count( $this->options ) )
                    $value = null;
                else{
                    $value = $keys[ $value - 1 ];
                }               
                
                if( $value === null )
                    Console::writeLine( $this->invalid );
                
            }while( $value === null );

            return $value;

        }
        
    }