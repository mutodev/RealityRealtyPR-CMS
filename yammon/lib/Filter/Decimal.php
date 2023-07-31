<?php

    class Filter_Decimal extends Filter{

        public function filter( $value ){

            return rtrim(trim($value, '0'), '.');

        }

    }
