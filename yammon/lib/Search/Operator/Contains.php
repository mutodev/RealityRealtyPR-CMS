<?php

    class Search_Operator_Contains extends Search_Operator{

        /* Returns the operator */
        function operator( ){
            return '~';
        }

        /* Returns a caption for the operator */
        function description( ){
            return t("Contains");
        }

        /* Returns the dql for that value */
        function compile( $field , $value ){
            $connection = Doctrine_Manager::connection();
            $value = $connection->quote( '%'.$value.'%' );
            return "$field LIKE $value";
        }

    }
