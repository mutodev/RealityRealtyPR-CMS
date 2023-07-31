<?php

	class Action_File{

        protected  $parts           = array();
        protected  $parts_stack     = array();

	    protected  $file            = null;
        protected  $view            = null;
        protected  $layout          = null;

        protected  $bootstrap       = true;
        protected  $autoload        = true;
        protected  $autorender      = true;
        protected  $shutdown        = true;

        protected  $started         = false;
        protected  $bootstraped     = false;
        protected  $autoloaded      = false;
        protected  $ended           = false;
        protected  $shutdowned      = false;
        protected  $rendered        = false;

        protected  $bootstrap_vars  = array();

	    public function __construct( $file ){
	        $this->file       = $file;
            $this->view       = new View();
            $this->started    = false;
            $this->ended      = false;
            $this->rendered   = false;
            $this->autorender = true;
	    }

        public function aborted(){
            return connection_status() != CONNECTION_NORMAL;
        }

        public function autoload(){

            if( !$this->getAutoLoading() ){
                return false;
            }

            if( $this->hasAutoloaded() )
                return false;

            $this->event( 'action.autoloading' );

            //Find Configuration Files
            $dirname = dirname( $this->file );
            $files   = FS::find( "*.yml" , FS::NON_RECURSIVE , $dirname , FS::FIND_MODE_FILES );

            //Load Helpers with configuration
            foreach( $files as $filename ){

                $helper_name = basename( $filename , ".yml" );
                $helper      = $this->helper( $helper_name );
                if( $helper ) $helper->loadOptions( $filename );
            }

            $this->event( 'action.autoloaded' );
            $this->autoloaded = true;

        }

        public function bootstrap(){

            if( !$this->getBootstrapping() ){
                return false;
            }
            if( $this->hasBootstraped() ){
                return false;
            }

            //Call pre bootstraps event and receive vars (If any)
            extract( (array) $this->event( 'action.bootstraping' )->getReturnValue(), EXTR_REFS );

           	$_BOOTSTRAP_PATTERN = dirname( $this->file )."/bootstrap.php";
           	$_BOOTSTRAP_FILES   = FS::findBackwards( $_BOOTSTRAP_PATTERN , Router::getPaths()  , FS::FIND_MODE_FILES );
            foreach( $_BOOTSTRAP_FILES as $_BOOTSTRAP_FILE ){
                require_once( $_BOOTSTRAP_FILE );
            }

            //Call post bootstraps event and receive vars (If any)
            extract( (array) $this->event( 'action.bootstraped' )->getReturnValue() );
            $this->bootstraped = true;

            //Save the boostraped vars
            $bootstrap_vars = get_defined_vars();
            unset( $bootstrap_vars['_BOOTSTRAP_PATTERN'] );
            unset( $bootstrap_vars['_BOOTSTRAP_FILES'] );
            unset( $bootstrap_vars['_BOOTSTRAP_FILE'] );
            $this->bootstrap_vars = $bootstrap_vars;


        }

        public function clear( ){
            return $this->view->clear( $key );
        }

        public function component( $class , $name = null ){
            return Helper::factory( $class , $name );
        }

        public function delete( $key ){
            return $this->view->delete( $key );
        }

        public function end( $file = null ){

            if( $this->hasEnded() )
                return false;

            if( $this->getAutoRender() ){
                $this->render( $file );
            }

            $this->event( 'controller.ending' );
            $this->event( 'action.ending' );
            $this->shutdown();
            $this->event( 'action.ended' );
            $this->event( 'controller.ended' );
            $this->ended = true;

        }

        public function endPart( ){

            $name = array_pop( $this->parts_stack );
            if( $name === null )
                return null;

            $content = ob_get_clean();
            if( $this->isPartRequested( $name ) )
                $this->parts[ $name ] = $content;

            return $content;

        }

        public function exists( $key ){
            return $this->view->exists( $key );
        }

	    protected function event( $name ){
            $event = new Event( $this , $name );
            return Event::notify( $event );
	    }

        public function get( $key , $default = null ){
            return $this->view->get( $key , $default );
        }

        public function getAutoLoading( ){
            return $this->autoload;
        }

        public function getAutoRender( ){
            return $this->autorender;
        }

        public function getBootstrapping(){
            return $this->bootstrap;
        }

        public function getComponents( $first = false ){
            return Helper::getInstances( $first );
        }

        public function getFile(){
            return $this->file;
        }

        public function getHelpers( $first = false ){
            return Helper::getInstances( $first );
        }

        public function getLayout( ){
            return $this->layout;
        }

        public function getShutdown(){
            return $this->shutdown;
        }

        public function getState(){

            if( $this->hasEnded() )
                return Action::ENDED;

            if( $this->hasRendered() )
                return Action::RENDERED;

            if( $this->hasStarted() )
                return Action::STARTED;

            return Action::STARTING;

        }

	    public function getView(){
	        return $this->view;
	    }

        public function hasAutoloaded(){
            return $this->autoloaded;
        }

        public function hasBootstraped(){
            return $this->bootstraped;
        }

        public function hasEnded(){
            return $this->ended;
        }

        public function hasRendered(){
            return $this->rendered;
        }

        public function hasShutdowned(){
            return $this->shutdowned;
        }

        public function hasStarted(){
            return $this->started;
        }

        public function helper( $class , $name = null ){
            return Helper::factory( $class , $name );
        }

        public function isPartRequest( ){

            if( !isset( $_SERVER['HTTP_X_YAMMON_REQUEST'] ) )
                return false;

            return $_SERVER['HTTP_X_YAMMON_REQUEST'] == "PARTS";

        }

        public function isPartRequested( $name = null ){

            if( !$this->isPartRequest( ) )
                return true;

            if( !isset( $_SERVER['HTTP_X_YAMMON_PARTS'] ) )
                return false;

            if( count( $this->parts_stack ) )
                return true;

            $parts = $_SERVER['HTTP_X_YAMMON_PARTS'];
            $parts = explode( "," , $parts );
            return in_array( $name , $parts );

        }

        public function render( $file = null , $output = true ){

            if( $this->hasRendered() ){
                return false;
            }

            //Get the view file
            if( $file == null ){
                $file = basename( $this->file , ".php" );
            }

            if( !FS::isFile( $file ) ){
                $dirname  = dirname( $this->file )."/";
                $basename = basename( $file );
                $file     = $dirname.$basename.".phtml";
            }

            //Auto set the helpers
            $helpers = $this->getHelpers( true );
            foreach( $helpers as $helper ){

                $class       = get_class( $helper );
                $helper_name = substr( $class , strpos( $class , "_" ) + 1  );
                if( !$this->exists( $helper_name ) ){
                    $this->set( $helper_name , $helper );
                }

            }

            $this->event( 'action.render' );

            //Render the view
            $content  = $this->view->render( $file );

            //Render the Layout
            if( $this->layout !== false ){
                $this->event( 'action.layout' );
                $layout = new Layout( );
                $layout->set( $this->view->toArray() );
                $layout->setContent( $content );
                $layout->setLayout( $this->layout );
                $content = $layout->render( );
                $this->event( 'action.layedout' );
            }

            //If its a part request
            if( $this->isPartRequest() ){

                $parts = $_SERVER['HTTP_X_YAMMON_PARTS'];
                $parts = explode( "," , $parts );

                foreach( $this->parts as $k => $v ){
                    if( !in_array( $k , $parts ) ){
                        unset( $this->parts[ $k ] );
                    }
                }

                if( count( $parts ) === 1 )
                    $content = array_shift( $this->parts );
                else
                    $content = json_encode( $this->parts );

            }

            //Output
            if( $output ){
                echo $content;
            }
            $this->event( 'action.rendered' );
            $this->rendered = true;

            return $content;

        }

        public function run( ){

            //Change To The File
            $dirname = realpath(dirname( $this->file).DS ).DS;

            //Add the folder to the include path
            $include_path = get_include_path();
            set_include_path( $include_path . PATH_SEPARATOR . $dirname );

            //Change the cwd
            $cwd = getcwd();
            chdir( $dirname );

            //Start
            $this->start();

            //Extract boostrap vars
            extract( $this->bootstrap_vars );

            //Include the file
            include( $this->file );

            //End
            $this->end();

            //Restore Include path
            set_include_path( $include_path );

            //Restore cwd
            chdir( $cwd );

        }

        public function set( $key , $value = null ){
            return $this->view->set( $key , $value );
        }

        public function setAutoLoading( $b = true ){
            return $this->autoload = !!$b;
        }

        public function setAutoRender( $b = true ){
            return $this->autorender = !!$b;
        }

        public function setBootstrapping( $b = true ){
            return $this->bootstrap = !!$b;
        }

        public function setLayout( $layout ){
            $this->layout = $layout;
        }

        public function setNoTimeLimit( $bool = true ){
            if( $bool )
                $this->setTimeLimit( 0 );
            else
                $this->setTimeLimit( 30 );
        }

        public function setShutdown( $b = true ){
            return $this->shutdown = !!$b;
        }

        public function setTimeLimit( $limit = 30 ){
            if( $limit < 0 )
                $limit = 0;
            set_time_limit( $limit );
        }

	    public function setAbortable( $bool = true ){
	        ignore_user_abort( !$bool );
	    }

        public function shutdown(){

            if( !$this->getShutdown() ){
                return false;
            }

            if( $this->hasShutdowned() ){
                return false;
            }

            $this->event( 'action.shutdown' );
            $pattern = dirname( $this->file )."/shutdown.php";
            $files   = FS::findBackwards( $pattern , Router::getPaths()  , FS::FIND_MODE_FILES );
            FS::requireFilesOnce( $files );
            $this->event( 'action.shutdowned' );
            $this->shutdowned = true;

        }

        public function start(){

            if( $this->started )
                return false;

            $this->event( 'controller.starting' );
            $this->event( 'action.starting' );
            $this->bootstrap();
            $this->autoload();
            $this->event( 'action.started' );
            $this->event( 'controller.started' );

            $this->started = true;

        }

        public function startPart( $name ){
            $this->parts[ $name ] = null;
            $this->parts_stack[]  = $name;
            ob_start();
            return $this->isPartRequested( $name );
        }

	}

