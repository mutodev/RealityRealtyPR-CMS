<?php

    class Search_Term{

        public $parser;    
        public $depth;
        public $booleanop;
        public $positive;
        public $field;
        public $operator;
        public $value;

        public function __construct( $parser ){
            $this->parser = $parser;
        }
        
        public function compile( $formatted = false ){
                                                    
            //Dont search empty values
            if( is_array( $this->value ) ){
                if( empty( $this->value ) ){
                    return null;                
                }
            }elseif( trim( $this->value ) == '' ){
                return null;
            }
                
            //Get the fields
            $fields = array();        
            if( $this->field == "*" )
                $fields = $this->parser->getDefaultFields( );
            else{
                $field = $this->parser->getField( $this->field );
                if( $field ) $fields[] = $field;
            }
                
            if( empty( $fields ) ){
                return null;
            }

            if( !is_array($this->value ) )
                $values = array( $this->value );
            else
                $values = $this->value;

            //Create Term
            $term = array();
            $field_count = count( $fields );
            foreach( $fields as $field ){

                //Get the operator            
                if( $this->operator == "*" ){
                    $operator = $field->getDefaultOperator();                    
                }else{
                    $operator = Search_Operator::factory( $this->operator );
                }            

              
                //Get the sources
                $sources = $field->getSources();

                //Get Boolean
                $values_boolean = $field->getValuesBoolean();
                $source_boolean = $field->getSourcesBoolean();
                if( count( $values ) < count( $sources ) )
                    $source_boolean = 'OR';



                //Create term
                $term2 = array();                
                foreach( $sources as $source ){
                    $term3 = array();

                    foreach( $values as $value ){
                    
                        //Filter Invalid Values
                        if( $field_count != 1 && !$field->validValue( $value ) ){
                            continue;
                        }
                        
						$value = $field->getType()->value( $value );

                        if( $formatted )
                            $term3[] = "\n      ".$operator->compile( $source , $value );
                        else    
                            $term3[] = $operator->compile( $source , $value );                        
                    }
                                      
                    if( !empty( $term3 ) )
                        if( $formatted )
                            $term2[] = "\n     (".implode( "\n      $values_boolean" , $term3 )."\n     )";
                        else
                            $term2[] = " ( ".implode( " $values_boolean " , $term3 )." ) ";                        
                }

                if( !empty( $term2 ) )  
                    if( $formatted )
                        $term[] = "\n   (".implode( "\n     $source_boolean" , $term2 )."\n   )";
                    else                        
                        $term[] = " ( ".implode( " $source_boolean " , $term2 )." ) ";            
            }

            //Return the result         
            if( empty( $term ) )
                return null;

            if( $formatted )
                $term = "\n  (" .implode( "\n   OR" , $term )."\n  )"; 
            else                
                $term = " ( " .implode( " OR " , $term )." ) "; 
                
            if( !$this->positive )
                $term = " ( NOT $term )";
                          
            return $term;                

        }
            
    }
