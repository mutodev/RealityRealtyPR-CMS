<?php

    class Search_Operator_Equals extends Search_Operator{

        /* Returns the operator */
        function operator( ){
            return '=';
        }

        /* Returns a caption for the operator */
        function description( ){
            return t("Is");
        }

        /* Returns the dql for that value */
        function compile( $field , $value ){

            $check_is_null     = false;
            $is_null_val       = null;
            $check_is_not_null = false;
            $is_not_null_val   = null;

            if( array_key_exists( 'null' , $this->options ) ){
                $check_is_null = true;
                $is_null_val   = $this->options['null'];
            }
            
            if( array_key_exists( 'notnull' , $this->options ) ){
                $check_is_not_null = true;
                $is_not_null_val   = $this->options['notnull'];
            }
                        
            if( $check_is_null && $value == $is_null_val ){
                return "$field IS NULL";
            }elseif( $check_is_not_null && $value == $is_not_null_val ){
                return "$field IS NOT NULL";
            }else{
                $connection = Doctrine_Manager::connection();
                $value = $connection->quote( $value );
                return "$field = $value";            
            }
            
        }

    }
