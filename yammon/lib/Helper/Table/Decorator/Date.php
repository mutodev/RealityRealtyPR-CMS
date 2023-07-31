<?php

    class Helper_Table_Decorator_Date extends Helper_Table_Decorator{

        public function apply( $value ){
            return tdate( "long" , $value );
        }
    
    }
