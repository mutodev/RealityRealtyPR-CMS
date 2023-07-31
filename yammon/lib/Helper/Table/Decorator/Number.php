<?php

    class Helper_Table_Decorator_Number extends Helper_Table_Decorator{

        public function apply( $value ){
            return number_format($value);
        }

    }
