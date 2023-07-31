<?php

    class Helper_Text extends Helper{

        public function excerpt( $value , $length = 160 ){

            $value = trim( $value );
            if( strlen( $value ) <= $length ){
                return $value;
            }else{
                return substr( $value , 0 , $length )."...";
            }
        
        }
        
        public function phone( $phone ){
        
            //Remove Anything not numeric out of the phone
            $clean_phone = preg_replace("/[^0-9]/" , "" , $phone);
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
            }else{
                return '';
            }

        }

        public function email( $email ){

            if( empty( $email ) ){
                return "";
            }
            return "<a href='mailto:$email'> $email </a>";
        }



    }
