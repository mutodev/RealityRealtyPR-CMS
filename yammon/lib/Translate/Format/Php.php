<?php

    class Translate_Format_Php extends Translate_Format{
        
        public static function load( $filename ){

            if( FS::isFile( $filename ) )
                $return = include( $filename );
            else
                $return = null;
                
            if( is_object( $return ) && $return instanceof Translate_Strings )
                return $return;
            else
                return new Translate_Strings( $return );
                
        }
        
        public static function save( Translate_Strings $strings , $filename ){
            $data = "<"."?php return ".var_export( $strings , true ).";";
            file_put_contents( $filename , $data );
            return true;
        }

    }