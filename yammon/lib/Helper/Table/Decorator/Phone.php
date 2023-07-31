<?php

    class Helper_Table_Decorator_Phone extends Helper_Table_Decorator{

        public function apply( $value ){

            //Remove Anything not numeric out of the phone
            $clean_phone = preg_replace("/[^0-9]/" , "" , $value );
            $length      = strlen( $clean_phone );

            //Separate phone into parts
            if( $length == 7 ){
                $part1 = '';
                $part2 = substr( $clean_phone , 0 , 3 );
                $part3 = substr( $clean_phone , 4 , 4 );
                return $part2.'-'.$part3;
            }if( $length == 10 ){
                $part1 = substr( $clean_phone , 0 , 3 );
                $part2 = substr( $clean_phone , 3 , 3 );
                $part3 = substr( $clean_phone , 6 , 4 );
                return '('.$part1.') '.$part2.'-'.$part3;
            }if( $length == 11 ){
                $part1 = substr( $clean_phone , 0 , 1 );
                $part2 = substr( $clean_phone , 1 , 3 );
                $part3 = substr( $clean_phone , 4 , 3 );
                $part4 = substr( $clean_phone , 7 , 4 );
                return '('.$part2.') '.$part3.'-'.$part4;
            }else{
                return $value;
            }

        }

    }
