<?php

    class Search_Operator_IsNull extends Search_Operator{

        /* Returns the operator */
        function operator( ){
            return '*';
        }

        /* Returns a caption for the operator */
        function description( ){
            return t("Is Null");
        }

        /* Returns the dql for that value */
        function compile( $field , $value ){
			if( $value )
            	return "$field IS NULL";
			else{
				return "$field IS NOT NULL";
			}
        }

    }
