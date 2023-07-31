<?php

    class Doctrine_Query_Yammon extends Doctrine_Query{
        
        function addWhereInNull( $expr, $params, $boolean = true ){
           $params = (array) $params;
           if( empty( $params ) ){
                if( $boolean )
                   $this->addWhere( $expr . " IS NULL" );
                else                   
                   $this->addWhere( $expr . " IS NOT NULL" );
            }else{                   
               $this->addWhereIn( $expr , $params , $boolean );
            }   
        }
        
        function andWhereNotInNull( $expr, $params ){
            $this->addWhereInNull( $expr , $params , true );
        }        
        
    }
