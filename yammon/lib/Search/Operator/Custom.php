<?php

    class Search_Operator_Custom extends Search_Operator{
        
        protected static $index = 0;
        
        /* Returns the dql for that value */
        function compile( $field , $value ){

            self::$index++;

            $connection = Doctrine_Manager::connection();
            $value = $connection->quote( $value );
            $value = trim( $value , "\'\"" );
            
            if( isset( $this->options['expression'] ))
                $expr = $this->options['expression'];
            else
                $expr = $this->options[0];
                
            $t = new Template( $expr );
            $expr = $t->apply( array( 'field' => $field , 'value' => $value, 'index' => self::$index ) );
                        
            return $expr;
            
        }

    }
