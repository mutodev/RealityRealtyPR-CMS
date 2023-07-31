<?php

    class Cache_Memory extends Cache_Base{
    
        protected $values     = array();
        protected $ttl_values = array();
        
        public function _exists( $key )
        {
            if( !array_key_exists( $key , $this->values ) )
                return false;
            
            if( $ttl != 0 && $this->ttl_values[$key] < time() ){
                unset( $this->values[$key] );
                unset( $this->ttl_values[$key] );             
                return false;
            }
            
            return true;

        }

        public function _get( $key , $default = null )
        {
            if( !$this->_exists( $key) )
                return $default;

            return $this->values[$key];
            
        }

        public function _set( $key , $value , $tags = null )
        {
            $this->values[$key]     = $value;
            $this->ttl_values[$key] = time() + $this->ttl;
        }
        
        public function _delete( $key )
        {
            unset( $this->values[$key] );
            unset( $this->ttl_values[$key] );            
        }        

        public function _clear( $tags = null )
        {
            $this->values     = array();
            $this->ttl_values = array();            
        }
    
    }