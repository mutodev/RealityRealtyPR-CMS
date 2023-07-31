<?php

    class Search_Lexer{

        private $length;    
        private $text;    
        private $position;
    
        const TOKEN_STRING                = 'string';
        const TOKEN_SQUOTED_STRING        = 'sstring';
        const TOKEN_DQUOTED_STRING        = 'dstring';        
        const TOKEN_FIELD                 = 'field';
        const TOKEN_OPEN_PARENTHESIS      = 'open_parenthesis';
        const TOKEN_CLOSE_PARENTHESIS     = 'close_parenthesis';
        const TOKEN_NOT                   = 'NOT';
        const TOKEN_OR                    = 'OR';
        const TOKEN_AND                   = 'AND';
        //const TOKEN_XOR                   = 'XOR';
        const TOKEN_OPERATOR              = 'operator';
        const TOKEN_COMMA                 = 'comma';        
                                                                    
        private $whitespace = array( ' ' , "\n" , "\t" , "\r" , "\0" , "\x0B" );
        private $breaking   = array( '(' , ')' , '-' , ':' , '<' , '>' , '=' , "~" , "!" , "," , '^' , '$' );
                
        public function __construct( $text ){
            $this->text     = $text;
            $this->length   = strlen( $text );
            $this->position = 0;
        }
        /* ---------------------------------------------------- */
        public function finished(){
            return $this->position >= $this->length;
        }
        /* ---------------------------------------------------- */
        public function is_whitespace( $position ){
                        
            if( $position >= $this->length )
                return false;
            else
                return in_array( $this->text{ $position } , $this->whitespace );
                
        }
        /* ---------------------------------------------------- */
        public function is_breaking( $position ){
                        
            if( $position >= $this->length )
                return true;
            else
                return in_array( $this->text{ $position } , $this->whitespace ) || in_array( $this->text{ $position } , $this->breaking );
                
        }        
        /* ---------------------------------------------------- */        
        public function remaining(){        
            return substr( $this->text , $this->position );
        }
        /* ---------------------------------------------------- */        
        public function next(){
            
            //Make sure we have input
            if( $this->position >= $this->length )
                return null;
             
            //Skip any whitespace
            while( $this->is_whitespace( $this->position ) ){
                $this->position++;
            }

            //Make sure we have input ( again )
            if( $this->position >= $this->length )
                return null;
            
            //Get the next char
            $token_char = $this->text{ $this->position };

            //Check if its a breaking character
            if( $token_char == "(" ){

                $this->position++;
                return array( self::TOKEN_OPEN_PARENTHESIS , $token_char );

            }elseif( $token_char == ")" ){

                $this->position++;
                return array( self::TOKEN_CLOSE_PARENTHESIS , $token_char );

            }elseif( $token_char == "-" ){
                
                $this->position++;
                return array( self::TOKEN_NOT , $token_char );
                
            }elseif( $token_char == "=" ){

                $this->position++;
                return array( self::TOKEN_OPERATOR , $token_char );            

            }elseif( $token_char == "~" ){

                $this->position++;
                return array( self::TOKEN_OPERATOR , $token_char );            

            }elseif( $token_char == "^" ){

                $this->position++;
                return array( self::TOKEN_OPERATOR , $token_char );

            }elseif( $token_char == "$" ){

                $this->position++;
                return array( self::TOKEN_OPERATOR , $token_char );

            }elseif( $token_char == "!" ){

                if( @$this->text{ $this->position + 1} == '=' ){
                    $this->position += 2;
                    return array( self::TOKEN_OPERATOR , "!=" ); 
                }elseif( @$this->text{ $this->position + 1} == '~' ){
                    $this->position += 2;
                    return array( self::TOKEN_OPERATOR , "!~" ); 
                }else{
                    $this->position += 1;
                    return array( self::TOKEN_NOT , $token_char );
                }
                
            }elseif( $token_char == "<" ){

                if( @$this->text{ $this->position + 1} != '=' ){
                    $this->position += 1;
                    return array( self::TOKEN_OPERATOR , "<" ); 
                }else{
                    $this->position += 2;
                    return array( self::TOKEN_OPERATOR , "<=" );
                }
                    
            }elseif( $token_char == ">" ){

                if( @$this->text{ $this->position + 1} != '=' ){
                    $this->position += 1;
                    return array( self::TOKEN_OPERATOR , ">" ); 
                }else{
                    $this->position += 2;
                    return array( self::TOKEN_OPERATOR , ">=" );
                }
                
            }elseif( $token_char == "," ){            
                $this->position++;
                return array( self::TOKEN_COMMA , $token_char );            
            }

            //Check if its a literal
            if( $token_char == '"' ){

                //Find matching quote
                $next_position = $this->position + 1;
                while( $next_position < $this->length && $this->text{ $next_position } != '"' ){
                    $next_position++;
                }
                                            
                $token_value = substr( $this->text , $this->position + 1 , $next_position - $this->position - 1 );
                $this->position = $next_position + 1;
                return array( self::TOKEN_DQUOTED_STRING , $token_value );

            }

            if( $token_char == "'" ){

                //Find matching quote
                $next_position = $this->position + 1;
                while( $next_position < $this->length && $this->text{ $next_position } != "'" ){
                    $next_position++;
                }
                                            
                $token_value = substr( $this->text , $this->position + 1 , $next_position - $this->position - 1 );
                $this->position = $next_position + 1;
                return array( self::TOKEN_SQUOTED_STRING , $token_value );

            }

            //Ok get the token until we find a breaking character
            $next_position = $this->position + 1;
            while( $next_position < $this->length && !$this->is_breaking( $next_position ) ){
                $next_position++;
            }
                        
            $token_value     = substr( $this->text , $this->position  , $next_position - $this->position );
            $token_length    = strlen( $token_value );            

            //Check if the token value is some special values
            if( $token_value == "OR" ){
                $this->position = $next_position;
                return array( self::TOKEN_OR , $token_value );
            }elseif( $token_value == "AND" ){
                $this->position = $next_position;                
                return array( self::TOKEN_AND , $token_value );
            }
            
/*          Removed Support of XOR because DQL doesn't support it
            }elseif( $token_value == "XOR" ){
                $this->position = $next_position;                
                return array( self::TOKEN_XOR , $token_value );
            }
*/
                          
            //Check if its a field
            if( isset( $this->text{ $next_position } ) && $this->text{ $next_position } == ":" ){
                $this->position = $next_position + 1;
                return array( self::TOKEN_FIELD , $token_value );
            }
            
            //Then its a regular string
            $this->position = $next_position;            
            return array( self::TOKEN_STRING , $token_value );

        }
        /* ---------------------------------------------------- */        
        public function peek( $c = 1 ){
            $position       = $this->position;
            $peek           = null;
            for( $i = 0 ; $i < $c ; $i++ ){
                $peek = $this->next();    
            }
            $this->position = $position;
            return $peek;
        }
        /* ---------------------------------------------------- */        
    
    }
    
