<?php

    class Session{

        private static $instance = null;

        private function __construct()
        {

        }

        static public function singleton()
        {

            $handler = Configure::read('session.handler' , 'Php' );
            $handler = ucwords( $handler );

            if (!isset(self::$instance)) {
                $className = __CLASS__.'_'.$handler;
                self::$instance = new $className;
            }

            return self::$instance;

        }

        public function __clone()
        {
            trigger_error('Clone is not allowed.', E_USER_ERROR);
        }

        public function __wakeup()
        {
            trigger_error('Unserializing is not allowed.', E_USER_ERROR);
        }

        static public function start( )
        {
            return self::singleton()->start();
        }

        static public function started()
        {
            return self::singleton()->started();
        }

        static public function close()
        {
            return self::singleton()->close();
        }

        static public function destroy()
        {
            return self::singleton()->destroy();
        }

        static public function regenerate( $delete = false )
        {
            return self::singleton()->regenerate( $delete );
        }

        static public function exists( $key )
        {
            return self::singleton()->exists( $key );
        }

        static public function write( $key , $value = null )
        {
            return self::singleton()->write( $key , $value );
        }

        static public function read( $key , $default = null )
        {
            return self::singleton()->read( $key , $default );
        }

        static public function delete(  $key  )
        {
            return self::singleton()->delete( $key );
        }

        static public function push(  $key  , $value = null )
        {
            return self::singleton()->push( $key , $value );
        }

        static public function pop( $key , $default = null )
        {
            return self::singleton()->pop( $key , $default );
        }

        static public function without( $key , $value )
        {
            return self::singleton()->without( $key , $value );
        }

        static public function peek( $key , $default = null )
        {
            return self::singleton()->peek( $key , $default );
        }

    }
