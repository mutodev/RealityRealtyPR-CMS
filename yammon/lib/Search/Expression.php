<?php

    class Search_Expression{

        public $parser;
        public $booleanop;
        public $positive = true;    
        public $terms    = array();

        public function __construct( $parser ){
            $this->parser = $parser;
        }

        function add( $term ){
            $this->terms[] = $term;
        }
        
        function compile( ){

            //Dont search empty values
            if( empty( $this->terms ) )
                return null;
  
            $first      = true;       
            $expression = array();
            foreach( $this->terms as $term ){
            
                $term_compiled = $term->compile( );
                
                if( $term_compiled === null )
                    continue;
 
                if( !$first ){
                    $expression[] = $term->booleanop;
                }
                $first = false;
                                   
                $expression[] =  $term_compiled;
                 
            }
         
            if( empty( $expression ) )
                return null;
         
            $expression = "(" . implode( " " , $expression ) . ")";

            if( !$this->positive ){
                $expression = " ( NOT ".$expression. " )";
            }
                               
            return $expression;

        }
            
    }
