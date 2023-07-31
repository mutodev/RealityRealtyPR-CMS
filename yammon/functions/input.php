<?php

	/* ------------------------------------------------- */
	function p( $input ){
		return purify( $input );
	}
	/* ------------------------------------------------- */
	function purify( $input ){

        global $purifier;
	
  	    if( empty( $purifier ) ){
		
			//Require the file
			vendor('HTMLPurifier' , 'HTMLPurifier.auto.php' );

			//Configure the purifier
			$path   = Yammon::getCachePath('HTMLPurifier');
			$config = HTMLPurifier_Config::createDefault();
			$config->set('Core.Encoding'  , 'UTF-8');
			$config->set('HTML.TidyLevel' , 'heavy' );
			$config->set('Cache.SerializerPath' , $path );            
			$purifier = new HTMLPurifier($config);
			
		 }
	
		 return $purifier->purify( $input );
	
	}
	/* ------------------------------------------------- */
    function input( $input , $key , $default = "" , $mode = 'none' ){

        if( !is_array( $key ) )
            $path  = explode( "." , $key );
        else
            $path  = $key;

        $key   = array_pop( $path );       
        $array = $input;
                                
        foreach( $path as $p ){
                
            if( isset( $array[ $p ] ) )
                $array = $array[ $p ];
            else
                return $default;

        }
        
        if( !is_array( $array ) ){
            return $default;
	    }elseif( !isset( $array[$key] ) ) {
            return $default;
        }
        
	    if( !is_array( $array[$key] ) ){

            if( $mode == 'html' ){
       	       $array[$key] = purify( $array[$key] );	    
            }elseif( $mode == 'text' ){
               $array[$key] = strip_tags( $array[$key] );
            }

            $array[$key] = trim( $array[$key] );
            
        }

        return $array[$key];
        			
    }
	/* ------------------------------------------------- */
    function get( $key , $default = "" , $mode = 'text' ){
        return input( $_GET , $key , $default , $mode );
    }
	/* ------------------------------------------------- */
    function post( $key , $default = "" , $mode = 'text' ){
        return input( $_POST , $key , $default , $mode );
    }
	/* ------------------------------------------------- */
    function request( $key , $default = "" , $mode = 'text' ){
        return input( $_REQUEST , $key , $default , $mode );
    }
	/* ------------------------------------------------- */
    function cookie( $key , $default = "" , $mode = 'text' ){
        return input( $_COOKIE , $key , $default , $mode );
    }
	/* ------------------------------------------------- */