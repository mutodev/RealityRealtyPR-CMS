<?php

    class Cache_APC extends Cache_Base{
    
        public function _exists( $key )
        {
            return apc_exists( $key );
        }

        public function _get( $key , $default = null )
        {
            $success = false;
            $value   = apc_fetch( $key , $success );   
            if( $success === false )
                return $default;
            else
                return $value;
        }

        public function _set( $key , $value  )
        {
            apc_store( $key , $value , $this->ttl );            
        }

        public function _delete( $key )
        {
            apc_delete( $key );            
        }

        public function _clear( $tags = null )
        {
            apc_clear_cache('user');            
        }
    
    }