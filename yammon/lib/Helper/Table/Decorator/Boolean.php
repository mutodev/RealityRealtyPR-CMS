<?php

    class Helper_Table_Decorator_Boolean extends Helper_Table_Decorator{

        public function apply( $value ){
            return $value ? "<i class=\"fa fa-check\"></i>" : '';
        }

    }
