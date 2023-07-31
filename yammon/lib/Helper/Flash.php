<?php

    class Helper_Flash extends Helper{


        public function __construct( $name , $options = array() ){
            parent::__construct( $name , $options );
           $Css = Helper::factory('Css');
           $Css->add( "/yammon/public/flash/css/flash.css" );

        }
		/* ---------------------------------------------------- */
		public function has( $class = null ){

			$msg       = Session::read("FLASH_MESSAGE" );
			$msg_class = Session::read("FLASH_CLASS" );

			return !empty( $msg ) && ( $class === null || $msg_class == $class );

		}
		/* ---------------------------------------------------- */
		public function set( $msg , $class = "" , $redirect = null ){

		    $Request = helper('Request');

		    Session::write( "FLASH_MESSAGE" , $msg );
		    Session::write( "FLASH_CLASS"   , $class );

		    if( $redirect !== null ){
		        redirect( $redirect );
		    }

		}
		/* ---------------------------------------------------- */
		public function get( $class = null , $framed = true ){

            $Html = helper("html");

			$msg       = Session::read("FLASH_MESSAGE" );
			$msg_class = Session::read("FLASH_CLASS" );

	        if( $class !== null && $msg_class != $class )
	            return null;

			if( empty($msg)  )
	            return null;

			$this->clear();
			if( $framed ) {
                $html = array();

                $html[] = "<div class='alert alert-$msg_class'>";
                $html[] =   "<button aria-hidden='true' data-dismiss='alert' class='close' type='button'>×</button>";
                $html[] =   $msg;
                $html[] = "</div>";

				return implode("\n", $html);
            }
            else {
                return $msg;
            }
		}
		/* ---------------------------------------------------- */
        public function render(){
            return $this->get();
        }
		/* ---------------------------------------------------- */
		public function __toString(){
		    return (string)$this->get();
		}
		/* ---------------------------------------------------- */
		public function error( $msg , $redirect = null ){
		    return $this->set( $msg , "danger" , $redirect );
        }
		/* ---------------------------------------------------- */
		public function warning( $msg , $redirect = null ){
		    return $this->set( $msg , "warning" , $redirect );
        }
		/* ---------------------------------------------------- */
		public function success( $msg , $redirect = null){
		    return $this->set( $msg , "success" , $redirect);
        }
		/* ---------------------------------------------------- */
		public function getWarning( $framed = true ){
		    return $this->get( "warning" , $framed );
		}
		/* ---------------------------------------------------- */
		public function getError(  $framed = true ){
		    return $this->get( "danger" , $framed );
		}
		/* ---------------------------------------------------- */
		public function getSuccess( $framed = true ){
		    return $this->get( "success", $framed );
		}
		/* ---------------------------------------------------- */
		public function clear(){
            Session::delete("FLASH_MESSAGE");
            Session::delete("FLASH_CLASS");
		}

	}

