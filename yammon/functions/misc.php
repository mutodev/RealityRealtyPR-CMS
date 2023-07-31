<?php

    //===============================================================
    function component( $class , $name = null , $options = array() ){
        return Component::factory( $class , $name , $options );
    }
    //===============================================================
    function decorator( $name , $value , $options = array() ){
        $name  = ucfirst( $name );
        $class = "Helper_Table_Decorator_$name";
        $obj   = new $class;
        return $obj->apply( $value , $options );
    }
    //===============================================================
    function helper( $class , $name = null , $options = array() ){
        return Helper::factory( $class , $name , $options );
    }
    //===============================================================
    function array_substract( $source , $substract ){
        foreach( $source as $k => $v ){
            if( in_array( $v , $substract ) ){
                unset( $source[ $k ] );
            }
        }
        return array_values($source);
    }
    //===============================================================
    function redirect( $href = "/" , $permanent = false , $convert = true ){

        //Convert the href to a url
        $href = $convert ? url( $href ) : $href;

        //Remove new lines to prevent header injection
        $href = str_replace(array("\r", "\r\n", "\n"), '', $href );

        //Uncomment this to test redirects
//        echo "REDIRECT: <a href='$href'>$href</a>";exit;

        //Redirect
        header("Location: $href" , true , $permanent ? 301 : 302 );

        //Stop execution
        exit();

    }
    //===============================================================
    function vendor( $name , $file = null ){
        return Yammon::loadVendor( $name , $file );
    }
    //===============================================================
    function iff( $condition , $ontrue = null , $onfalse = '' ){

        if( $ontrue === null )
            $ontrue = $condition;

        if( $condition )
            return $ontrue;
        else
            return $onfalse;

    }
    //===============================================================
