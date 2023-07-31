<?php

    class Filter_Phone extends Filter{
        
        public function filter( $value ){

            $v = preg_replace( "[^0-9]" , "" , $value );
            $v = substr( $v , 0 , 10 );
            if( $v === false )
                return "";
            else
                return $v;

        }
    
    }
