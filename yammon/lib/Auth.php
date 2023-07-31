<?php

    //TODO: Settings
    //TODO: Cache
    //TODO: Persist

    class Auth{

        private static $authenticator = null;
        private static $authorizer    = null;
        private static $settings      = null;

        public static function authenticator(){
            if( self::$authenticator )
                return self::$authenticator;

            $class = Configure::read("auth.class.authenticator", 'Auth_Authenticator');

            return self::$authenticator = new $class();
        }

        public static function authorizer(){
            if( self::$authorizer )
                return self::$authorizer;

            $class = Configure::read("auth.class.authorizer", 'Auth_Authorizer');

            return self::$authorizer = new $class();
        }

        public static function settings(){
            if( self::$settings )
                return self::$settings;

            $class = Configure::read("auth.class.settings", 'Auth_Settings');

            return self::$settings = new $class();
        }

        /* Authenticator =================================== */
        public static function get( $key = null ){
            return self::authenticator()->get( $key );
        }

        public static function getId( ){
            return self::authenticator()->getId( );
        }

        public static function getRealId( ){
            return self::authenticator()->getRealId( );
        }

        public static function hash( $string , $salt = null ){
            return self::authenticator()->hash( $string , $salt );
        }

        public static function isLoggedIn(){
            return self::authenticator()->isLoggedIn();
        }

        public static function login( $username , $password , $persist = false , $target = null ){
            return self::authenticator()->login( $username , $password , $persist , $target );
        }

        public static function forceLogin( $account ){
            return self::authenticator()->forceLogin( $account );
        }

        public static function logout(  ){
            return self::authenticator()->logout();
        }

        public static function mask( $id ){
            return self::authenticator()->mask( $id );
        }

        public static function requireLogin( $redirect = null ){
            return self::authenticator()->requireLogin( $redirect );
        }

        public static function salt( ){
            return self::authenticator()->salt( );
        }

        public static function setPassword( $Account , $password , $save = false ){
            return self::authenticator()->setPassword( $Account , $password , $save );
        }

        /* Authorizer =================================== */
        public static function addRule( $requester , $permission ,  $resources = null , $value = true ){
            return self::authorizer()->addRule( $requester , $permission , $resources , $value );
        }

        public static function findRules( $requester = null , $exact = true ){
            return self::authorizer()->findRules( $requester , $exact );
        }

        public static function findRulesQuery( $requester = null , $exact = true ){
            return self::authorizer()->findRulesQuery( $requester , $exact );
        }

        public static function hasPermission( $permission , $resource = null , $requester = null ){
            return self::authorizer()->hasPermission( $permission , $resource , $requester );
        }

        public static function requirePermission( $permission , $resource = null , $requester = null ){
            return self::authorizer()->requirePermission( $permission , $resource , $requester );
        }

        public static function removeRule( $rule ){
            return self::authorizer()->removeRule( $rule );
        }

        public static function addAccountToRole( $Account , $Role ){
            return self::authorizer()->addAccountToRole( $Account , $Role );
        }

        public static function removeAccountFromRole( $Account , $Role ){
            return self::authorizer()->removeAccountFromRole( $Account , $Role );
        }

        public static function hasRole( $Role , $Account = null ){
            return self::authorizer()->hasRole( $Role , $Account );
        }

        public static function getAccountRoles( $Account = null ){
            return self::authorizer()->getAccountRoles( $Account );
        }

        public static function getAccountRolesIds( $Account ){
            return self::authorizer()->getAccountRolesIds( $Account );
        }

        public static function getChildRoles( $Role ){
            return self::authorizer()->getChildRoles( $Role );
        }

        public static function getChildRolesIds( $Role ){
            return self::authorizer()->getChildRolesIds( $Role );
        }

        public static function getParentRoles( $Role ){
            return self::authorizer()->getParentRoles( $Role );
        }

        public static function getParentRolesIds( $Role ){
            return self::authorizer()->getParentRolesIds( $Role );
        }

        public static function getRoleAccounts( $Role ){
            return self::authorizer()->getRoleAccounts( $Role );
        }

        public static function getRoleAccountsIds( $Role ){
            return self::authorizer()->getRoleAccountsIds( $Role );
        }

        /* Settings =================================== */
        public static function getSetting( $Setting , $requester = null , $exact = false ){
            return self::settings()->getSetting( $Setting , $requester , $exact );
        }

        public static function setSetting( $Setting , $value , $requester = null  ){
            return self::settings()->setSetting( $Setting , $value , $requester );
        }


    }
