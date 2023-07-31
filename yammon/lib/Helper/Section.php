<?php

    class Helper_Section extends Helper{

        private $contents = array();
        private $stack    = array();

        public function start( $name , $append = true ){
            $name = strtolower( $name );

            $this->stack[] = $name;
            if( !$append || !isset( $this->contents[ $name ] ) )
                $this->contents[ $name ] = array();

            ob_start();

        }

        public function end(){

            if( !count($this->stack) )
                throw new Exception( "There is currently no opened section" );

            $name = array_shift( $this->stack );
            $name = strtolower( $name );
            $this->contents[ $name ][] = ob_get_clean();

            return null;
        }

        public function exists( $name ){
            $name = strtolower( $name );
            return isset( $this->contents[ $name ] );
        }

        public function remove( $name ){
            $name = strtolower( $name );
            unset( $this->contents[ $name ] );
        }

        public function render( $name ){

            $name = strtolower( $name );

            if( isset( $this->contents[ $name ] ) )
                return implode( "\n" , $this->contents[ $name ] );
            else
                return '';

        }

    }
