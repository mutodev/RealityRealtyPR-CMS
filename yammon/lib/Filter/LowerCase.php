<?php

    class Filter_LowerCase extends Filter{

        public function filter( $value ){
            return strtolower( $value );
        }

    }
