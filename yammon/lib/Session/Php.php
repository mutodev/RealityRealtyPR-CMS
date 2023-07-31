<?php

    class Session_Php extends Session_Base{

        protected $avoid_locks   = false;
        protected $writes        = array();
        protected $deletes       = array();
        protected $destroyed     = false;

        public function __construct(){
            $this->avoid_locks = Configure::read('session.avoid_locks' , false );
        }

        public function __destruct()
        {
            //Close the session
            if( $this->avoid_locks )
                $this->close();
        }

        public function start( ){

            //Dont Start the session twice
            if( $this->started() ){
                return false;
            }

            //Start the session
            session_start();

            //Close the session right away
            if( $this->avoid_locks )
                session_write_close();

        }

        public function started()
        {
            if( $this->destroyed )
                return false;

            $session_id = session_id();
            return $session_id != "";
        }

        public function close()
        {

            //Check if we really need to save
            if( $this->avoid_locks && empty( $this->writes ) && empty( $this->deletes ) )
                return false;

            if( $this->destroyed )
                return false;

            //Start the session
            if( !isset( $_SESSION ) ){
                session_start();
            }else{

                //Make sure we don't loose any data
                //added to the session directly
                $BACKUP = $_SESSION;
                @session_start();
                $_SESSION = $BACKUP;

            }

            //Add the delayed writes to the session
            foreach( $this->writes as $k => $v ){
                $_SESSION[$k] = $v;
            }

            //Delete the keys from the session
            foreach( $this->deletes as $k  ){
                unset( $_SESSION[ $k ] );
            }

            //Close the session
            session_write_close();
            return true;

        }

        public function destroy()
        {

            //Start the session
            @session_start();

            //Mark as destroyed
            $this->destroyed = true;

            //Destroy it
            session_destroy();

        }

        public function regenerate( $delete = false )
        {
            @session_regenerate_id( (bool)$delete );
        }

        public function exists( $key )
        {
            //Start the session
            $this->start( );

            //Check if the value exists
            return array_key_exists( $key , $_SESSION );
        }

        public function write( $key , $value = null )
        {

            //Set the value on the session
            if( $this->avoid_locks ){

                //Check if we really, really have to save the session
                if( isset( $_SESSION ) && array_key_exists( $key , $_SESSION )&& $_SESSION[$key] === $value )
                    return $value;

                $this->writes[ $key ] = $value;
                unset( $this->deletes[$key] );
            }else{
                $this->start();
                $_SESSION[ $key ] = $value;
            }

            return $value;

        }

        public function read( $key , $default = null )
        {

            //Start the session
            $this->start();

            //Get the value
            if( array_key_exists( $key , $this->writes ) ){
                $value = $this->writes[ $key ];
            }elseif( array_key_exists( $key , $_SESSION ) ){
                $value = $_SESSION[ $key ];
            }else{
                $value = $default;
            }

            return $value;

        }

        public function delete(  $key  )
        {

            //Start the session
            $this->start( );

            //Get the current value
            $value = $this->read( $key );

            //Remove the value
            if( $this->avoid_locks ){
                $this->deletes[] = $key;
                unset( $this->writes[$key] );
            }
            unset( $_SESSION[ $key ] );

            return $value;

        }

    }
