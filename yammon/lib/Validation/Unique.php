<?php

    class Validation_Unique extends Validation{

        protected function setupOptions(){
            parent::setupOptions();
            $this->setOption( "message" , "%{label} must be unique" );
            $this->addOptions( array(
                "source"    => ""    ,
                "source_id" => ""    ,
                "id"        => ""    ,
                "exact"     => false
            ));
        }

        protected function valid( $value , $context = null ){

            $source    = $this->getOption("source");
            $source_id = $this->getOption("source_id");
            $exact     = $this->getOption("exact");
            $id        = $this->getOption("id");

            //Get the source
            $source = explode( "." , $source );
            $table  = array_shift( $source );
            $field  = array_shift( $source );

            if( empty( $table ) || empty( $field ) )
                return false;

            //Query the database for uniqueness
            $query = new Doctrine_Query();
            $query->from( $table );
            $query->select( $field );

            if( $exact )
                $query->where( "$field = ?" , $value );
            else
                $query->where( "$field LIKE ?" , $value );

            if( !empty($source_id) && !empty($id) ){
                $query->andwhere( "$source_id <> ?" , $id );
            }

            $count = $query->count();

            //Return true if there is no other record with the same value
            return $count == 0;

        }

    }

