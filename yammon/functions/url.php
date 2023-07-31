<?php

    function url( $action = null ){
        $args = func_get_args();
        return call_user_func_array( array("Router" , "url" ) , $args );
    }

    function qs( ){

        $args = func_get_args();

        if( count( $args ) == 1 && is_array( $args[0] ) )
            $params = $args[0];
        elseif( count( $args ) > 1 )
             for( $i = 0 ; $i < count( $args ) ; $i = $i + 2 )
                $params[ $args[ $i ] ] = $args[ $i + 1 ];
        else
            $params = array();


	    $return = array();
	    $oparams = $params;
	    $params = array_merge( $_GET , $params );
	    $i      = 0 ;
	    $c      = count($params);

	    foreach( $params as $key => $value ){

            //Dont include null values
            if( $value === null ){
                continue;
            }

		    if( is_array($value) ){
			    $return[] = urldecode(http_build_query(array($key => $value)));
		    }else{
			    $return[] = urlencode($key)."=".urlencode($value);
		    }

	    }

	    if( empty( $return ) )
	        return "?";
        else
            return "?" . implode( "&" , $return );

    }
