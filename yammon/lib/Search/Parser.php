<?php

    /*
        Grammar:

        expression              -> and_expression   OR  expression     | and_expression
        and_expression          -> paren_expression AND and_expression | paren_expression
        paren_expression        -> - ( expression ) | ( expression ) | sub_expression
        sub_expression          -> field: operator value | field: value | value | - sub_expression
        value                   -> [VALUE]

    */

    class Search_Parser{

        private $lexer;
        private $fields                = array();
        private $expressions           = array();
        private $text                  = "";
        private $boolean_operators     = array();
        private $default_bool_operator = "AND";
        private $positive_expression   = true;
        private $errors                = array();

        /* ---------------------------------------------------- */
        public function __construct( $text = null ){
            if( $text !== null )
                $this->text = $text;
        }
        /* ---------------------------------------------------- */
        public function getErrors(){
            return $this->errors;
        }
        /* ---------------------------------------------------- */
        public function addField( $name , $options = array() ){
            $this->fields[ $name ] = new Search_Field( $name , $options );
        }
        public function removeField( $name){
            unset($this->fields[ $name ]);
        }
        /* ---------------------------------------------------- */
        public function addDefaultField( $name ){
            $field = $this->getField( $name );
            if( $field !== null )
            $field->setDefault( true );
        }
        /* ---------------------------------------------------- */
        public function addDefaultFields( $names ){
            foreach( $names as $name ){
                $this->addDefaultField( $name );
            }
        }
        /* ---------------------------------------------------- */
        public function getField( $field ){
            if( !isset( $this->fields[ $field ]) ){
                return null;
            }else{
                return $this->fields[ $field ];
            }
        }
        /* ---------------------------------------------------- */
        public function setDefaultBooleanOperator( $op ){
            if( in_array( $op , array("AND" , "OR") ) )
                $this->default_bool_operator = $op;
        }
        /* ---------------------------------------------------- */
        public function isField( $field ){
            return $this->getField( $field ) !== null;
        }
        /* ---------------------------------------------------- */
        public function getFields( ){
            return $this->fields;
        }
        /* ---------------------------------------------------- */
        public function getDefaultFields(){

            $defaults = array();
            foreach( $this->fields as $field ){
                if( $field->isDefault() ){
                    $defaults[] = $field;
                }
            }

            return $defaults;

        }
        /* ---------------------------------------------------- */
        public function parse( $text = null ){

            if( $text === null )
                 $text = $this->text;

            $this->text                = $text;
            $this->errors              = array();
            $this->lexer               = new Search_Lexer( $text );
            $this->boolean_operators[] = $this->default_bool_operator;
            $this->expressions[]       = new Search_Expression( $this );

            while( !$this->lexer->finished() ){
                $this->boolean_operators = array( $this->default_bool_operator );
                $this->expression();
            }

            //Return the expression
            $expression = array_pop( $this->expressions );

            return $expression->compile();

        }
        /* ---------------------------------------------------- */
        private function expect( $expected_type ){

            list( $peek_type , $peek_value ) = $this->lexer->peek();

            if( $expected_type != $peek_type  ){
                $this->error( "expected $expected_type got $peek_value" );
            }else
                $this->lexer->next();

        }
        /* ---------------------------------------------------- */
        private function error( $string ){
            $this->errors[] = $string;
            $this->lexer->next(); //Try to recover
        }
        /* ---------------------------------------------------- */
        private function expression( ){

            $this->and_expression();
            list( $peek_type , $peek_value ) = $this->lexer->peek();
            if( $peek_type == Search_Lexer::TOKEN_OR ){

                array_push( $this->boolean_operators , Search_Lexer::TOKEN_OR );

                $this->lexer->next();
                $this->expression();

                array_pop( $this->boolean_operators );

            }

        }
        /* ---------------------------------------------------- */
        private function and_expression( ){

            $this->paren_expression();
            list( $peek_type , $peek_value ) = $this->lexer->peek();
            if( $peek_type == Search_Lexer::TOKEN_AND ){

                array_push( $this->boolean_operators , Search_Lexer::TOKEN_AND );

                $this->lexer->next();
                $this->and_expression();

                array_pop( $this->boolean_operators );

            }

        }
        /* ---------------------------------------------------- */
        private function paren_expression( ){

            list( $peek_type , $peek_value )   = $this->lexer->peek( 1 );
            list( $peek2_type , $peek2_value ) = $this->lexer->peek( 2 );

            $invert = false;
            if( $peek_type  == Search_Lexer::TOKEN_NOT &&
                $peek2_type == Search_Lexer::TOKEN_OPEN_PARENTHESIS ){

                $invert                    = true;
                $this->positive_expression = !$this->positive_expression;

                //Consume not
                $this->lexer->next(  );
                list( $peek_type , $peek_value )   = $this->lexer->peek( 1 );

            }

            if( $peek_type == Search_Lexer::TOKEN_OPEN_PARENTHESIS ){

                //Start Expression
                $expression = new Search_Expression( $this );
                array_push( $this->expressions , $expression );

                $this->lexer->next();
                $this->expression();
                $this->expect( Search_Lexer::TOKEN_CLOSE_PARENTHESIS );

                if( $invert ){
                    $this->positive_expression = !$this->positive_expression;
                }

                //End Expression
                array_pop( $this->expressions );
                $parent_expression = $this->expressions[ count( $this->expressions ) - 1 ];

                $expression->booleanop = $this->boolean_operators[ count( $this->boolean_operators ) -1 ];
                $expression->positive  = $this->positive_expression;
                $parent_expression->add( $expression );

            }else{
                $this->sub_expression();
            }

        }
        /* ---------------------------------------------------- */
        private function sub_expression( ){


            list( $peek1_type , $peek1_value ) = $this->lexer->peek( 1 );
            list( $peek2_type , $peek2_value ) = $this->lexer->peek( 2 );
            list( $peek3_type , $peek3_value ) = $this->lexer->peek( 3 );

            if( $peek1_type == Search_Lexer::TOKEN_NOT ){

                $this->positive_expression = !$this->positive_expression;

                $this->lexer->next();
                $this->sub_expression();

                $this->positive_expression = !$this->positive_expression;

            }else{

                $expression          = $this->expressions[ count( $this->expressions ) -1 ];
                $boolean_operator    = $this->boolean_operators[ count( $this->boolean_operators ) -1 ];
                $positive_expression = $this->positive_expression;
                $field               = "*";
                $operator            = "*";
                $value               = "";

                if( in_array( $peek1_type , array( Search_Lexer::TOKEN_STRING , Search_Lexer::TOKEN_SQUOTED_STRING , Search_Lexer::TOKEN_DQUOTED_STRING ))){

                    //$this->lexer->next();
                    //$value = $peek1_value;
                    $value = $this->value();

                }elseif( $peek1_type == Search_Lexer::TOKEN_FIELD ){

                    $this->lexer->next();
                    $field = $peek1_value;

                    if( in_array( $peek2_type , array( Search_Lexer::TOKEN_STRING , Search_Lexer::TOKEN_SQUOTED_STRING , Search_Lexer::TOKEN_DQUOTED_STRING ))){

                       // $this->lexer->next();
                       // $value = $peek2_value;
                       $value = $this->value();

                    }elseif( $peek2_type == Search_Lexer::TOKEN_OPERATOR  ){

                        $this->lexer->next();
                        $operator = $peek2_value;

                        if( in_array( $peek3_type , array( Search_Lexer::TOKEN_STRING , Search_Lexer::TOKEN_SQUOTED_STRING , Search_Lexer::TOKEN_DQUOTED_STRING ))){
                            //$this->lexer->next();
                            //$value = $peek3_value;
                            $value = $this->value();
                        }else{
                            $this->error("unexpected input $peek3_value");
                            return false;
                        }

                    }else{
                        $this->error("unexpected input $peek2_value");
                        return false;
                    }

                }else{
                    $this->error("unexpected input $peek1_value");
                    return false;
                }

                $term = new Search_Term( $this );
                $term->booleanop  = $boolean_operator;
                $term->positive   = $positive_expression;
                $term->field      = $field;
                $term->operator   = $operator;
                $term->value      = $value;
                $expression->add( $term );

            }

        }
        /* ---------------------------------------------------- */
        private function value(){

            $values = array();
            $stop   = false;
            do{

                list( $peek1_type , $peek1_value ) = $this->lexer->peek( 1 );
                list( $peek2_type , $peek2_value ) = $this->lexer->peek( 2 );

                //If its a double quoted string, return as is
                if( $peek1_type == Search_Lexer::TOKEN_DQUOTED_STRING ){
                    $values[] = $peek1_value;
                }

                //If it is a single quoted string, separtate by space and commas
                if( $peek1_type == Search_Lexer::TOKEN_SQUOTED_STRING ){

                    $value = trim( $peek1_value );
                    $value = preg_replace('/,/'    , ' ', $value);
                    $value = preg_replace('/\s\s+/', ' ', $value);
                    $value = explode( ' ' , $value );
                    if( !count( $value ) )
                        $value = array('');
                    $values = array_merge( $values , $value );

                }

                //Then it must be a single value
                if( $peek1_type == Search_Lexer::TOKEN_STRING ){
                    $values[] = $peek1_value;
                }

                //Consume the value
                $this->lexer->next();

                //Check if we keep doing this
                if( $peek2_type == Search_Lexer::TOKEN_COMMA ){
                    //Consume comma
                    $this->lexer->next();
                }elseif( !in_array( $peek2_type , array( Search_Lexer::TOKEN_STRING , Search_Lexer::TOKEN_SQUOTED_STRING , Search_Lexer::TOKEN_DQUOTED_STRING ))){
                    $stop = true;
                }

            }while( !$stop );

            //Return the value
            if( count( $values ) == 1 ){
                return $values[0];
            }else{
                return $values;
            }

        }
    }


