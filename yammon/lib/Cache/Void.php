<?php

    class Cache_Void extends Cache_Base{
    
        public function _exists( $key )
        {
            return false;
        }

        public function _get( $key , $default = null )
        {
            return $default;
        }

        public function _set( $key , $value )
        {
        }
        
        public function _delete( $key )
        {
        }        

        public function _clear( )
        {
        }
    
    }
