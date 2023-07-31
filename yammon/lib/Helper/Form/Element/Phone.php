<?php

    class Helper_Form_Element_Phone extends Helper_Form_Element_Input
    {
        public function setupOptions()
        {
            parent::setupOptions();
            $this->setOption('type' , 'phone' );
        }

		public function construct()
        {
            $this->addValidation('phone');
            $this->addClass('ym-form-text');
            $this->addClass('ym-form-phone');
            $this->addClass('form-control');
		}

        public function getFormattedValue()
        {
            //Format the value
            $value = $this->getValue();

            if( $value ){

                //Remove Anything not numeric out of the phone
                $clean_phone = preg_replace("/[^0-9]/" , '' , $value );
                $length      = strlen( $clean_phone );

                //Separate phone into parts
                if( $length == 7 ){
                    $part1 = '';
                    $part2 = substr( $clean_phone , 0 , 3 );
                    $part3 = substr( $clean_phone , 4 , 4 );

                    $value = $part2.'-'.$part3;

                }
                else if ( $length == 10 ){
                    $part1 = substr( $clean_phone , 0 , 3 );
                    $part2 = substr( $clean_phone , 3 , 3 );
                    $part3 = substr( $clean_phone , 6 , 4 );

                    $value = '('.$part1.') '.$part2.'-'.$part3;
                }
                else if( $length == 11 ){
                    $part1 = substr( $clean_phone , 0 , 1 );
                    $part2 = substr( $clean_phone , 1 , 3 );
                    $part3 = substr( $clean_phone , 4 , 3 );
                    $part4 = substr( $clean_phone , 7 , 4 );

                    $value = '('.$part2.') '.$part3.'-'.$part4;
                }
            }

            return $value;
        }

        public function getValue()
        {
            $value = parent::getValue();

            if (is_null($value)) {
                return $value;
            }

            //Remove Anything not numeric out of the phone
            $value = preg_replace("/[^0-9]/" , '' , $value );

            if (strlen($value) == 10) {
                $value = '+1' . $value;
            }
            if (strlen($value) == 11) {
                $value = '+' . $value;
            }

            return $value;
        }
    }

