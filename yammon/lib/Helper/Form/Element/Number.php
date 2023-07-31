<?php

    class Helper_Form_Element_Number extends Helper_Form_Element_Text
    {
		public function construct()
        {
		    parent::construct();

            $this->addValidation('Numeric');
		}

        public function setupOptions()
        {
            parent::setupOptions();
        }

        public function getValue()
        {
            $value = parent::getValue();

            if( $value == null )
                return null;

            $fvalue = trim(str_replace(',', '', $value ));
            if( !is_numeric( $fvalue ) )
                return $value;

            return (float)$fvalue;
        }

        public function getFormattedValue()
        {
            $value = $this->getValue();

            if( $value === null )
                return null;

            $fvalue = str_replace(',', '', $value );

            if( !is_numeric( $fvalue ) )
                return $value;

            $decimal = substr(strrchr($fvalue, "."), 1);
            $whole   = (int) floor($fvalue);

            $return = number_format( $whole );
            if( $decimal )
                $return .= '.'.$decimal;

            return $return;
        }
    }
