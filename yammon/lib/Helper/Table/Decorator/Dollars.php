<?php

    class Helper_Table_Decorator_Dollars extends Helper_Table_Decorator{

        public function apply( $value ){
            return "$".number_format( $value , 2 );
        }
    
    }
