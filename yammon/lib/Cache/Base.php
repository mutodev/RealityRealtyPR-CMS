<?php

    abstract class Cache_Base implements ArrayAccess
    
        protected $name      = '';
        protected $ttl       = 0;
        protected $prefix    = null;
        protected $index_key = null;
        
        public function __construct( $options = array() )
        {
        
            if( isset( $options['name'] ) )
                $this->setName( $options['name'] );

            if( isset( $options['ttl'] ) )
                $this->setTTL( $options['ttl'] );

            if( isset( $options['prefix'] ) )
                $this->setPrefix( $options['prefix'] );
            else
                $this->setPrefix( md5( APPLICATION_PATH ) );
        
            $this->index_key =  "___#index#___";
        
        }
        
        public function getName(  )
        {
            return $this->name;
        }
        
        public function getPrefix()
        {
            return $this->prefix;
        }
            
        public function setTTL( $ttl )
        {
            if( $ttl < 0 ) $ttl = 0;
            return $this->ttl = $ttl;
        }
        
        public function getTTL()
        {
            return $this->ttl;
        }
            
        public function __get( $key )
        {
            return $this->get( $key );
        }
        
        public function __set( $key , $value  )
        {
            return $this->set( $key , $value );
        }
        
        public function __isset( $key , $value  )
        {
            return $this->exists( $key );
        }    
    
        public function __unset( $key , $value  )
        {
            return $this->delete( $key );
        }
        
        public function offsetExists( $key )
        {
            return $this->exists( $key );
        }
    
        public function offsetGet( $key )
        {
            return $this->get( $key );
        }	
    
        public function offsetSet( $key, $value )
        {
            if( $key === null ) $key = '';
            return $this->set( $key , $value );            
        }	
        
        public function offsetUnset( $key )
        {
            return $this->delete( $key );
        }	    
        
        public function get( $key , $default = null )
        {
            $_key = $this->prefix.":".$this->name.":".$key;
            return $this->_get( $_key , $default );
        }

        public function set( $key , $value , $tags = null )
        {
            $_key = $this->prefix.":".$this->name.":".$key;
            $this->_set( $_key , $value );
            
            //Save the tags
            if( isset( $tags ) ){

                $tags      = (array) $tags;
                
                //Get the index
                $index_key = $this->prefix.":".$this->name.":".$this->index_key;
                $index     = $this->_get( $index_key , array() );

                //Modify the index
                foreach( $tags as $tag ){
                    $index[$tag][] = $_key;
                }
                
                //Store the index again
                $this->_set( $index_key , $index );
                
            }            
            
        }
        
        public function delete( $key )
        {
            $key = $this->prefix.":".$this->name.":".$key;
            return $this->_delete( $key );
        }        
                
        public function deleteByTags( $tags = array() )
        {
            $tags      = (array) $tags;                

            //Get the index
            $index_key = $this->prefix.":".$this->name.":".$this->index_key;
            $index     = $this->_get( $index_key , array() );
            
            //Remove the unwanted keys
            foreach( $tags as $tag ){
                if( !isset( $index[ $tag ] ) ) 
                    continue;

                foreach( $index[ $tag ] as $_key ){            
                    $this->_delete( $_key );
                }
                
                unset( $index[ $tag ] );
                
            }     
            
            //Resave the index
            $this->_set( $index_key , $index );
            
        }
        
        public function clear( )
        {
            return $this->_clear();
        }

        abstract protected function _exists( $key );        
        abstract protected function _get( $key , $default = null );
        abstract protected function _set( $key , $value );
        abstract protected function _delete( $key  );
        abstract protected function _clear( );
        
    }