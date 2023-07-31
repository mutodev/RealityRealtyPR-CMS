<?php

    abstract class Session_Base{

        abstract public function start( );
        abstract public function started();
        abstract public function close();
        abstract public function destroy();
        abstract public function regenerate( $delete = false );
        abstract public function exists( $key );
        abstract public function write( $key , $value = null );
        abstract public function read( $key , $default = null );
        abstract public function delete(  $key  );

        public function push(  $key  , $value = null )
        {
            $values   = (array) $this->read( $key );
            $values[] = $value;
            $this->write( $key , $values );
        }

        function pop( $key , $default = null )
        {
            $values   = (array)$this->read( $key , $default );
            $value    = count( $values ) ? array_pop( $values ) : null;

            if( count( $values ) )
                $this->write( $key , $values );
            else
                $this->delete( $key );

            return $value;
        }

        public function without( $key , $value )
        {
            $values = (array) $this->read( $key );
            foreach( $values as $k => $v ){
                if( $v == $value ) unset( $values[$k] );
            }
            $this->write( $key , $values );
        }

        public function peek( $key , $default = null )
        {
            $values   = (array)$this->read( $key , $default );
            $value    = count( $values ) ? $values[ count( $values ) - 1 ] : $default;
            return $value;
        }

    }
