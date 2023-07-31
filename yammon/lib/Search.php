<?php

class Search{

    protected $parser;

    public function __construct(){
        $this->parser = new Search_Parser();
    }

    public function addField( $field , $options ){
        $this->parser->addField( $field , $options );
    }

    public function removeField( $field ){
        $this->parser->removeField( $field );
    }

    public function addDefaultFields( $defaults ){
        $this->parser->addDefaultFields( $defaults );
    }

    public function getFields(){
        return $this->parser->getFields();
    }

    public function setOptions( $data ){

        //Get the source
        $source    = input( $data , 'source'    , null    );
        $fields    = input( $data , 'fields'    , array() );
        $defaults  = input( $data , 'defaults'  , array() );

        //Initialize the parser
        $this->parser = new Search_Parser();

        //If there are no fields fetch them from the database
        if( !empty( $source ) && empty( $fields )  ){

            $table   = Doctrine::getTable( $source );
            $columns = $table->getColumns();

            foreach( $columns as $column => $definition ){

                //Don't add this types
                if( in_array( $definition['type'] , array( 'array' , 'object' , 'blob' ) ) )
                    continue;

                $fields[ $column ] = array(
                    "source" => $source.".".$column
                );

            }

        }

        //Add Fields
        foreach( $fields as $field => $options ){
            $this->addField( $field , $options );
        }

        //Set default fields
        if( !empty( $defaults ) ){
            $defaults = (array) $defaults;
            $this->addDefaultFields( $defaults );
        }

        return $data;

    }

    public function search( $select = null , $query = '' , $bool = 'AND' ){

        if( is_array( $query ) )
            $query = $this->compile( $query );

        //Get the dql
        $dql = "";
        if( $query ){
            $this->parser->setDefaultBooleanOperator( $bool );
            $dql = $this->parser->parse( $query );
        }

        if( $select ){
            if( $dql ) $select->andWhere( $dql );
        }

        return $dql;

    }

    public function compile( $query ){

        //It's not an array we don't need to compile it
        if( !is_array( $query ) ){
            return trim( $query );
        }

        //Loop thru the array
        $result = array();
        foreach( $query as $k => $v ){

            if( is_array( $v ) )
                $v = implode( " , " , $v );

            $v = trim( $v );
            if( trim( $v ) == '' || $v === null )
                continue;

            if( strpos(  $v , "\'" ) === false && strpos(  $v , "\"" ) === false ){
                $v = "'$v'";
            }

            if( trim($k) == ' ' || $k == "*" || is_numeric( $k ) ){
                //Make sure that the defaults are first
                array_unshift( $result , $v );
            }else{
                $result[] = "$k: $v";
            }

        }
        $result = implode( " " , $result );
        return $result;
    }

}
