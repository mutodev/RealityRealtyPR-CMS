<?php

    class Inflector{

        private static $_cache = array();

        public static function camel( $string ){

            if( isset( self::$_cache[ __FUNCTION__ ][ $string ] ) )
                return self::$_cache[ __FUNCTION__ ][ $string ];

            if( $string == "" )
                return "";

            $result = $string;
            $result = preg_replace('/[^a-z0-9]/iu'  , ' ' , $result );
            $result = ucwords($result);
            $result = preg_replace('/\s+/u'         , '' , $result );
            return self::$_cache[ __FUNCTION__ ][ $string ] = $result;

        }

        public static function classify( $string ){

            if( isset( self::$_cache[ __FUNCTION__ ][ $string ] ) )
                return self::$_cache[ __FUNCTION__ ][ $string ];

            if( $string == "" )
                return "";

            $result = $string;
            $result = preg_replace('/\s\s+/u'      , '_' , $result );
            $result = preg_replace('/[^a-z0-9]/iu' , '_' , $result );
            $result = preg_replace('/__+/u'        , '_' , $result );
            $result = ucwords( $result );

            return self::$_cache[ __FUNCTION__ ][ $string ] = $result;

        }

        public static function dashify( $string ){

            if( isset( self::$_cache[ __FUNCTION__ ][ $string ] ) )
                return self::$_cache[ __FUNCTION__ ][ $string ];

            $result = strtolower($string);
            $result = preg_replace('/\s\s+/u'     , '-' , $result );
            $result = preg_replace('/[^a-z0-9]/u' , '-' , $result );
            $result = preg_replace('/__+/u'       , '-' , $result );

            return self::$_cache[ __FUNCTION__ ][ $string ] = $result;
        }

        public static function hyphenize( $string ){

            if( isset( self::$_cache[ __FUNCTION__ ][ $string ] ) )
                return self::$_cache[ __FUNCTION__ ][ $string ];

            $result = strtolower($string);
            $result = preg_replace('/\s\s+/u'     , '_' , $result );
            $result = preg_replace('/[^a-z0-9]/u' , '_' , $result );
            $result = preg_replace('/__+/u'       , '_' , $result );

            return self::$_cache[ __FUNCTION__ ][ $string ] = $result;

        }

        public static function dotify( $string ){

            if( isset( self::$_cache[ __FUNCTION__ ][ $string ] ) )
                return self::$_cache[ __FUNCTION__ ][ $string ];

            $result = $string;
            $result = preg_replace('/\_/u'    , '.', $result );
            $result = preg_replace('/\s\s+/u' , ' ', $result );
            $result = strtolower( $result );

            return self::$_cache[ __FUNCTION__ ][ $string ] = $result;

        }

        public static function humanize( $string ){

            if( isset( self::$_cache[ __FUNCTION__ ][ $string ] ) )
                return self::$_cache[ __FUNCTION__ ][ $string ];

            $result = $string;
            $result = preg_replace('/\_/u'    , ' ' , $result );
            $result = preg_replace('/\s\s+/u' , ' ' , $result );
            $result = ucfirst( $result );

            return self::$_cache[ __FUNCTION__ ][ $string ] = $result;

        }

        public static function normalize( $string , $whitespace = ' ' ){

            if( isset( self::$_cache[ __FUNCTION__ ][ $string ] ) )
                return self::$_cache[ __FUNCTION__ ][ $string ];

            $table = array(
                'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
                'À'=>'A', 'Á'=>'A', 'Â'=>'A',  'Ã'=>'A',  'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I',  'Í'=>'I',  'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
                'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O',  'Ù'=>'U',  'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
                'à'=>'a', 'á'=>'a', 'â'=>'a',  'ã'=>'a',  'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
                'ê'=>'e', 'ë'=>'e', 'ì'=>'i',  'í'=>'i',  'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
                'ô'=>'o', 'õ'=>'o', 'ö'=>'o',  'ø'=>'o',  'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
                'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',
            );


            //Remove any html characters
            $string = html_entity_decode( $string , ENT_QUOTES , 'UTF-8' );

            //Make Conversion of accents/ligatures
            $string = strtr( $string, $table );

            //Remove any extra utf characters lying arround after the change
            $regex = "/(  [\\x00-\\x7F]                  # single-byte sequences   0xxxxxxx
                        | [\\xC0-\\xDF][\\x80-\\xBF]     # double-byte sequences   110xxxxx 10xxxxxx
                        | [\\xE0-\\xEF][\\x80-\\xBF]{2}  # triple-byte sequences   1110xxxx 10xxxxxx * 2
                        | [\\xF0-\\xF7][\\x80-\\xBF]{3}  # quadruple-byte sequence 11110xxx 10xxxxxx * 3
                       )
                       | .                               # anything else
                       /xu";

            $result = preg_replace( $regex, '$1', $string );

            //Only allow letters and numbers
            $string = preg_replace('/[^A-Z0-9\s]/iu' , $whitespace , $string );

            //Remove any extra whitespace
            $string = preg_replace('/\s\s+/u'        , $whitespace , $string );

            //Trim the String
            $string = trim($string);

            //Convert to lower case
            $string = strtolower( $string );

            return self::$_cache[ __FUNCTION__ ][ $string ] = $result;

        }

    }

