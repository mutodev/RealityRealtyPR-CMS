<?php

    class Translate_Format{

        private static function getClassForFileName( $filename ){

            $info      = pathinfo( $filename );
            $extension = $info['extension'];
            $class     = 'Translate_Format_'.ucfirst( $extension );
            return $class;

        }

        public static function load( $filename )
        {
            $class = self::getClassForFileName( $filename );
            return call_user_func( array( $class , 'load' ) , $filename );

        }

        public static function save( Translate_Strings $strings , $filename ){

            $class = self::getClassForFileName( $filename );
            return call_user_func( array( $class , 'save' ) , $strings , $filename );

        }

    }