<?php

	class Action{

        const STARTING     = 0;
        const STARTED      = 1;
        const RENDERED     = 2;        
        const ENDED        = 3;

        protected static $instance = null;

        private function __construct(){
        
        }
                
        public function aborted(){
            return self::getInstance()->aborted();
        }                
                
        public static function autoload(){
            return self::getInstance()->autoload();        
        }        
        
        public static function bootstrap(){
            return self::getInstance()->bootstrap();
        }

        public static function clear(){
            return self::getInstance()->clear();
        }

        public function component( $class , $name = null ){
            return self::component( $class , $name );
        }

        public static function delete( $key ){
            return self::getInstance()->delete( $key );
        }

        public static function end( $file = null ){
            return self::getInstance()->end( $file );
        }

        public static function endPart(  ){
            return self::getInstance()->endPart();
        }

        public static function exists( $key ){
            return self::getInstance()->exists( $key );
        }

        public static function get( $key , $default = null ){
            return self::getInstance()->get( $key , $default );        
        }

        public static function getAutoLoading( ){
            return self::getInstance()->getAutoLoading( );
        }

        public static function getAutoRender( ){
            return self::getInstance()->getAutoRender( );
        }

        public static function getBootstrapping(){
            return self::getInstance()->getBootstrapping( );
        }

        public static function getLayout(){
            return self::getInstance()->getLayout( );
        }

        public static function getInstance(){
            return self::$instance;
        }

        public static function getShutdown(){
            return self::getInstance()->getShutdown( );
        }

        public static function getState(){
            return self::getInstance()->getState( );
        }
        
	    public static function getView(){
            return self::getInstance()->getView( );
	    }        
        
        public static function hasAutoloaded(){
            return self::getInstance()->hasAutoloaded( );
        }

        public static function hasBootstraped(){
            return self::getInstance()->hasBootstraped( );
        }

        public static function hasEnded(){
            return self::getInstance()->hasEnded( );
        }

        public static function hasRendered(){
            return self::getInstance()->hasRendered( );
        }

        public static function hasShutdowned(){
            return self::getInstance()->hasShutdowned( );
        }

        public static function hasStarted(){
            return self::getInstance()->hasStarted( );
        }

        public static function helper( $class , $name = null ){
            return self::getInstance()->helper( $class , $name = null );
        }

        public static function initialize( $file ){
            return self::$instance = new Action_File( $file );        
        }

        public static function isPartRequest( ){
            return self::getInstance()->isPartRequest(  );
        }
        
        public static function isPartRequested( $name ){
            return self::getInstance()->isPartRequested( $name );
        }

        public function lock( $timeout = null  ){
            return self::getInstance()->lock( $timeout );           
        }

        public static function render( $file = null , $output = true ){
            return self::getInstance()->render( $file , $output );
        }

        public static function run( $file ){
            return self::initialize( $file )->run();
        }

        public static function set( $key , $value = null ){
            return self::getInstance()->set( $key , $value  );
        }

        public static function setAutoLoading( $b = true ){
            return self::getInstance()->setAutoLoading( $b );
        }

        public static function setAutoRender( $b = true ){
            return self::getInstance()->setAutoRender( $b );
        }

        public static function setBootstrapping( $b = true ){
            return self::getInstance()->setBootstrapping( $b );
        }

        public static function setLayout( $layout ){
            return self::getInstance()->setLayout( $layout );
        }

        public static function setNoTimeLimit( $b = true ){
            return self::getInstance()->setNoTimeLimit( $b );
        }
        
        public static function setShutdown( $b = true ){
            return self::getInstance()->setShutdown( $b );
        }
	    
        public static function setTimeLimit( $limit = 30 ){
            return self::getInstance()->setTimeLimit( $limit );           
        }        
	    
	    public static function setAbortable( $b = true ){
            return self::getInstance()->setAbortable( $b );
	    }	    
	    
        public static function shutdown(){
            return self::getInstance()->shutdown( );    
        }	    
	    
        public static function start(){
            return self::getInstance()->start( ); 
        } 

        public static function startPart( $name ){
            return self::getInstance()->startPart( $name );
        }

	}

