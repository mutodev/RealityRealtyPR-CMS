<?php

    class Auth_Authenticator{

        protected $account           = null;
        protected $session_id        = "auth";
        protected $model             = "Account";
        protected $model_id          = "id";
        protected $model_username    = "username";
        protected $model_password    = "password_hash";
        protected $model_salt        = "password_salt";
        protected $model_active      = "active";
        protected $model_last_login  = "last_login";
        protected $model_last_attempt = "last_login_attempt";
        protected $model_login_tries = "login_tries";

        /**
         * Constructor
         */
        public function __construct()
        {

        }

        /**
         * Gets the currently logged in account , or a field on the account
         *
         * @param string $key               the field to get on the account or null
         * @return mixed                    the account or the field on the account
         */
        public function get( $key = null )
        {

            $id = Session::read( $this->session_id.".id" );

            if( empty( $id ) ){
                $this->account = null;
            }elseif( empty( $this->account ) ){

                $Query = new Doctrine_Query;
                $Query->from("{$this->model} m");
                $Query->leftJoin("m.Organization o");
                //$Query->leftJoin("o.Subscription.Plan");
                $Query->leftJoin("o.Agency");
                $Query->leftJoin("m.Company");
                $Query->where("m.id = ?", $id);

                $this->account = $Query->fetchOne();
            }

            if( empty( $this->account ) )
                return $this->account = null;

            if( $key === null )
                return $this->account;
            else
                return $this->account[ $key ];

        }

        /**
         * Gets the id of the currently logged account
         * its the same as calling  get('id')
         *
         * @return mixed                    the id of the account or null if there is no account logged in
         */
        public function getId()
        {
            return $this->get( $this->model_id );
        }

        /**
         * Gets the id of the real logged account
         *
         * @return mixed                    the id of the account or null if there is no account logged in
         */
        public function getRealId()
        {

            $real_id = Session::read( $this->session_id.".realid");
            if( $real_id )
                return $real_id;
            else
                return Auth::getId();

        }


        /**
         * Hash a string , using the global and optionally a local salt
         *
         * @param string $string            the string to hash
         * @param string $salt              optional local salt
         * @return string                   hashed string
         * @return string                   hashed string
         */
        public function hash( $string , $salt = null )
        {
            $global = Configure::read( 'auth.salt'  );
            return sha1( $global . $string . $salt );
        }

        /**
         * Checks if an account is logged in
         *
         * @return bool                   boolean determining if there is an account logged in
         */
        public function isLoggedIn( )
        {
            return self::get() !== null;
        }

        /**
         * Login Account
         *
         * @param string $username          username
         * @param string $password          password
         * @param bool $persist             should the user be remembered
         * @return mixed                    true or Auth_Authenticator_Error
         */
         public function login( $username , $password , $persist = false , $target = null )
         {

            $persist = (bool) $persist;

            //Logout user
            if( $this->isLoggedIn() )
                $this->logout();

            //Do sanity checks
            if( trim( $username ) == '' ){
                return new Auth_Authenticator_Error( Auth_Authenticator_Error::USERNAME );
            }

            if( trim( $password ) == '' ){
                return new Auth_Authenticator_Error( Auth_Authenticator_Error::PASSWORD );
            }

            //Check the ammount of tries ( session version )
            $max_tries        = Configure::read('auth.max_tries' );
            $lockout_interval = Configure::read('auth.lockout_interval'   , 60*30 );
            $tries            = Session::read( $this->session_id.".tries" , 0 );
            $last_try         = Session::read( $this->session_id.".last"  , 0 );

            if( $tries >= $max_tries ){
                if( $last_try + $lockout_interval > time() ){
                    return new Auth_Authenticator_Error( Auth_Authenticator_Error::TO_MANY_TRIES );
                }else{
                    Session::write( $this->session_id.".tries" , 0 );
                }
            }

            //Find the accounts for the username
            $q = new Doctrine_Query();
            $q->from( $this->model )
              ->where( $this->model_username . ' = ? ' , $username );

            $Accounts = $q->execute();

            //Check if there is an account
            if( !count( $Accounts ) )
                return new Auth_Authenticator_Error( Auth_Authenticator_Error::USERNAME );

            //Check if the username is Ambiguous
            if( count( $Accounts ) > 1 )
                return new Auth_Authenticator_Error( Auth_Authenticator_Error::AMBIGUOUS );

            //Get the Account
            $Account = $Accounts[0];

            //Check the ammount of tries ( db version )
            $max_tries        = Configure::read('auth.max_tries' );
            $lockout_interval = Configure::read('auth.lockout_interval' , 60*30 );
            $tries            = $this->model_login_tries  ? $Account[ $this->model_login_tries ]  : 0;
            $last_try         = $this->model_last_attempt ? $Account[ $this->model_last_attempt ] : 0;

            if( $tries >= $max_tries ){
                if( $last_try + $lockout_interval > time() ){
                    return new Auth_Authenticator_Error( Auth_Authenticator_Error::TO_MANY_TRIES );
                }else{
                    Session::write( $this->session_id.".tries" , 0 );
                }
            }

            //Check the password
            $salt = $this->model_salt ? $Account[ $this->model_salt ] : '';
            $hash = $this->hash( $password , $salt );


            if( $Account[ $this->model_password ] != $hash )
                return new Auth_Authenticator_Error( Auth_Authenticator_Error::PASSWORD );

            //Check that the account is active
            if( $this->model_active ){
                if( !$Account[ $this->model_active ] )
                    return new Auth_Authenticator_Error( Auth_Authenticator_Error::INACTIVE );
            }

            //Do Login
            $this->forceLogin( $Account );

            //Redirect
            if( $target === true || $target === null )
                $target = Configure::read('auth.onlogin');

            if( $target !== false ){
                redirect( $target );
            }

            return true;

         }

         public function forceLogin( $Account )
         {
            if( !($Account instanceof Account) )
                $Account = Doctrine::getTable('Account')->find( $Account );

            if( !$Account || !$Account->id )
                return false;

            //Save the information on the sesion
            Session::write( $this->session_id.".id"        , $Account[ $this->model_id ] );
            Session::write( $this->session_id.".automatic" , 0 );
            Session::write( $this->session_id.".tries"     , 0 );
            Session::write( $this->session_id.".last"      , 0 );

            //Save the information on the account
            if( $this->model_last_login )
                $Account[ $this->model_last_login ] = date('Y-m-d H:i:s');

            if( $this->model_login_tries )
                $Account[ $this->model_login_tries ] = 0;

            $Account->save();

            //Save the Account
            $this->account = $Account;

            return true;
         }

        /**
         * Logout Account
         *
         * @return boolean                 boolean if the account was logged out
         */
        public function logout()
        {

            //Check if the account is masked
            $real_id = Session::read( $this->session_id.".realid" );

            if( $real_id ){
                $this->account = null;
                Session::delete( $this->session_id.".realid");
                Session::write( $this->session_id.".id"        , $real_id );
                Session::write( $this->session_id.".automatic" , 1 );
                Session::write( $this->session_id.".tries"     , 0 );
                Session::write( $this->session_id.".last"      , 0 );
            }else{

                $this->account     = null;
                Session::delete( $this->session_id.".realid");
                Session::delete( $this->session_id.".id");
                Session::delete( $this->session_id.".automatic");
                Session::delete( $this->session_id.".tries");
                Session::delete( $this->session_id.".last");

            }


        }

        /**
         * Mask Account
         *
         * @return boolean                 boolean if the account was masked
         */
        public function mask( $id )
        {

            //Make sure this is logged in
            if( !$this->isLoggedIn() )
                return false;

            //Get current id
            $current_id = Session::read( $this->session_id.".realid" , Auth::getId() );

            //Check that the user and the mask are not the same
            if( $current_id == $id )
                return false;

            //Load the Account
            $account = Doctrine::getTable( $this->model )->find( $id );

            if( $account ){

                //Save Account
                $this->account = $account;

                //Save the Session Data
                Session::write( $this->session_id.".realid"    , $current_id );
                Session::write( $this->session_id.".id"        , $id );
                Session::write( $this->session_id.".automatic" , 1 );
                Session::write( $this->session_id.".tries"     , 0 );
                Session::write( $this->session_id.".last"      , 0 );
                return true;

            }else{
                return false;
            }

        }

        /**
         * Require Login
         *
         * This function will redirect the user to the
         * login page or return true if the user is logged in
         *
         * @param string $redirect        where to redirect , or null for default login page
         * @return boolean                true if the user is logged in
         */
         public function requireLogin( $redirect = null )
         {

            //Get the redirect url
            if( $redirect === null ){
                $target    = url(Configure::read('auth.login' , '/' ));
                $redirect  = $target.qs( array( "target" => $_SERVER['REQUEST_URI'] ) );
            }

            //Redirect if needed
            if( !$this->isLoggedIn() ){
                redirect( $redirect , false , false );
                exit();
            }

            return true;

         }

        /**
         * Create a new salt
         *
         * Create a new random salt
         *
         * @return string              random salt
         */
         public function salt()
         {
            return sha1( rand() );
         }

        /**
         * Set Password
         *
         * Hash the password and create a new salt for an account
         *
         * @param string $Account   an account object to set the password on
         * @param string $password  the new password to set
         * @param string $save      whether to save the changes
         * @return Account          Account with the password and salt set
         */
         public function setPassword( $Account , $password , $save = false )
         {

            $salt = $this->model_salt ? $Account[ $this->model_salt ] : '';
            $hash = $this->hash( $password , $salt );

            //Create a new salt
            $salt = '';
            if( $this->model_salt )
                $salt = $this->salt();

            //Set the fields on the account
            if( $this->model_salt )
                $Account[ $this->model_salt ]     = $salt;

            $Account[ $this->model_password ] = $this->hash( $password , $salt );

            //Save the model
            if( $save )
                $Account->save();

            return $Account;

         }

    }
