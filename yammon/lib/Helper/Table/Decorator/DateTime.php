<?php

    class Helper_Table_Decorator_DateTime extends Helper_Table_Decorator{

        public function apply( $value ){    
            return tdate( "long-time" , $value );
        }
    
    }
