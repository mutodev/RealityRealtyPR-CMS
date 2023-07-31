<?php

    class Helper_Form extends Helper_Form_Element_Container{

        public function __construct( $name = 'Form' , $options = array() , $parent = null ){

            //Load Css
            $Css = helper('css');
            $Css->add( "/yammon/public/form/css/default.css" );
            $Css->add( "/yammon/public/form/css/button.css" );

            //Load Javascript
            $Javascript = helper('Javascript');
            $Javascript->add( "/yammon/public/mootools/js/mootools.js" );
            $Javascript->add( "/yammon/public/mootools/js/mootools-more.js" );

            $Javascript->add( "/yammon/public/widget/widget.js" );
            $Javascript->add( "/yammon/public/widget/widget-form.js" );

            parent::__construct( $name , $options , $parent );

            //Allow to receive data as GZIP
            $this->handleContentType();

			//Connect Events
           	Event::connect( "action.started" , array( $this , "autoHandle" ) );

        }

        public function autoHandle( $event = null ){
			if( $this->getOption('autohandle') ){
				$this->handle();
			}
        }

        public function handle( ){

            $this->handleDepends();
            $this->handleNested( );

            //Notify Other Elements
            $event = new Event( $this , 'form.handle' );
            return Event::notify( $event );

        }

        public function setupOptions(){

            parent::setupOptions();
            $this->addOption( 'action'    , "" );
            $this->addOption( 'method'    , "POST" );
            $this->addOption( 'highlight' , true );
            $this->addOption( 'autohandle', true );
            $this->setOption( 'default_renderers' , array(
                'box_renderer' => array(
                    'type'    => '2Column' ,
                    'margin'  => true ,
                    'padding' => true ,
                    'border'  => true ,
                ),
                'description_renderer' => 'Inline'
            ));
        }

        public function handleContentType() {
            if ($_SERVER['CONTENT_TYPE'] == 'application/gzip') {
                $queryString = urldecode(gzdecode(base64_decode(file_get_contents('php://input'))));
                parse_str($queryString, $_POST);
            }
        }

        public function handleDepends( ){

            if( !isset( $_SERVER['HTTP_X_YAMMON_REQUEST']) )
                return false;

            if( $_SERVER['HTTP_X_YAMMON_REQUEST'] != 'HELPER_FORM-DEPENDS' )
                return false;

            $json = array();

            //Get the elements to check
            $element_names = $_SERVER['HTTP_X_YAMMON_REQUEST_DEPENDS'];
            $element_names = explode("," , $element_names );

            foreach( $element_names as $element_name ){
                $element = $this->getElement( $element_name );
                $json[ $element_name ] = $element->isVisible();
            }

            echo json_encode( $json );
            exit();
        }

        public function handleNested( ){

            if( !isset( $_SERVER['HTTP_X_YAMMON_REQUEST']) )
                return false;

            if( $_SERVER['HTTP_X_YAMMON_REQUEST'] != 'HELPER_FORM-NESTED' )
                return false;

            $json = array();

            //Get the elements to check
            $element_names = $_SERVER['HTTP_X_YAMMON_REQUEST_NESTED'];
            $element_names = explode("," , $element_names );

            foreach( $element_names as $element_name ){
                $element = $this->getElement( $element_name );
                $json[ $element_name ] = $element->renderBody();
            }

            echo json_encode( $json );
            exit();

        }

        protected function hasFileElements( $elements = null ){
            return false;
        }

        public function isSubmitted( $name = null ){

            $name   = $name === null ? $this->getName() : $name;
            $method = $this->getOption('method');

            //Check if the form was posted
            if( $method == 'POST' ){
                if( isset( $_POST[ $name ] ) ){
                    return true;
                }
            }else{
                if( isset( $_GET[ $name ] ) ){
                    return true;
                }
            }

            return false;

        }

        public function isValid(){

            if( $this->isSubmitted() ){
                return parent::isValid();
            }else{
                return false;
            }

        }

        public function start(){

            $Html   = helper('html');
            $output = array();

            //Override the Size Option
            if( !$this->getOption('size' ) ){
                $this->setOption('size' , 'auto');
            }

            //Construct the attributes
            $attributes                 = $this->getOption( "attributes");
            $attributes["id"]           = $this->getName();
            $attributes["name"]         = $this->getName();
            $attributes["method"]       = $this->getOption( "method");
            $attributes["class"]        = array("ym-form");
            $attributes["autovalidate"] = $this->getOption( "autovalidate");
            $attributes["widget"]       = "Form";

			$action = $this->getOption( "action" );
			if( $action )
				$action = url( $action );
			else
				$action = url(".").qs();
            $attributes["action"]       = $action;

            if( !$this->getOption( "highlight") )
                $attributes["class"][] = "form-no-highlight";

            if( $this->hasFileElements() )
                $attributes["enctype"] = "multipart/form-data";

            $output[] = $Html->startTag( "form" , $attributes );

			return implode( "\n" , $output );
        }

        public function end(){
            $Html = helper('html');
            return $Html->endTag( "form" );
        }

        public function render( ){

            $output   = array();
            $output[] = $this->start();
            $output[] = parent::render();
            $output[] = $this->end();

            return implode( "\n" , $output );

        }

    }
