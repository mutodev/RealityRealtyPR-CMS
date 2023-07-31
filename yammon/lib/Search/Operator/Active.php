<?php

class Search_Operator_Active extends Search_Operator{

    /* Returns the operator */
    function operator( ){
        return '>=';
    }

    /* Returns a caption for the operator */
    function description( ){
        return t("Greater or equal than");
    }

    /* Returns the dql for that value */
    function compile( $field , $value ){
        $connection = Doctrine_Manager::connection();
        $value = $connection->quote( $value );
        return "$field >= ".date('Y-m-d H:i:s');
    }

}
