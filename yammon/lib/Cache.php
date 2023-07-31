<?php

    class Cache{

        static $instances = array();

        public static function get( $name )
        {

            $name = strtolower( $name );
            if( isset( $this->instances[ $name ] ) ){
                return $this->instances[ $name ];
            }
            
            $instance = new Cache_Void();
            
            return $this->instances[ $name ] = $instance;
            
            
        }

    }