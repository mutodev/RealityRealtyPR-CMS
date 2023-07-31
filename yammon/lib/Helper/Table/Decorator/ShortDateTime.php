<?php

    class Helper_Table_Decorator_ShortDateTime extends Helper_Table_Decorator{

        public function apply( $value ){
            return tdate( "short-time" , $value );
        }

    }
