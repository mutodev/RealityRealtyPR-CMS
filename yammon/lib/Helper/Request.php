<?php

    class Helper_Request extends Helper{
    
        private $elements = array();
        
		function isPost( $name = null ){
			if( $name === null )
			   return (count( $_POST ) != 0);
			else
			   return isset( $_POST[$name] ) || isset( $_POST[ $name."_x"] );
		}

		function isAjax(){
			return @($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
		}

		function redirect( $url ){
		    redirect( $url );
			exit();
		}

    }
