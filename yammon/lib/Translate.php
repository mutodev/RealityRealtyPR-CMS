<?php

    class Translate{

        protected static $languages = null;
        protected static $language  = null;
        protected static $strings   = array();

        public static function t(){

            //Get the arguments
    		$args        = func_get_args();
	    	$str         = array_shift( $args );

    		//If there is no string return null
            if( $str === null ){
                return $str;
            }

    	    //Translate the string
            $strings     = self::load();
    	    $translation = $strings->get( $str );
    	    if( $translation !== null )
    	        $str = $translation;

	    	//Normalize Arguments and apply template
            $template = new Template( $str );
            foreach( $args as $i => $arg ){
                if( !is_array( $arg ) && !is_object( $arg ) ){
                    $arg = array( $i => $arg );
                }
                $str = $template->apply( $arg );
            }

            //Return the string
    		return $str;

        }

        protected static function getLanguagesPath(  )
        {
            $translation_path = Yammon::getWritablePath('translation');
            $languages_path   = Yammon::getWritablePath('translation/languages');
            return $languages_path;
        }

        public static function getLanguageFile( $language = null )
        {

            $language = $language ? $language : self::getLanguage();

            if( !self::isLanguage( $language ) )
                return null;

            $languages_path   = self::getLanguagesPath();
            $language_file    = $languages_path.$language.".php";
            return $language_file;

        }

        public static function getLanguages()
        {
            return (array) Configure::read('translation.languages');
        }

        public static function getLanguage( )
        {

            //Check if we already know our language
            if( self::$language )
                return self::$language;

            //Check from cookie
            if( !empty( $_COOKIE['lang'] ) ){
                $lang = $_COOKIE['lang'];
                if( self::isLanguage( $lang ) )
                    return self::$language = $lang;
            }

            //Check from session ( in case cookies are disabled )
/*
            if( Session::exists('lang') ){
                $lang = Session::read( 'lang' );
                if( self::isLanguage( $lang ) )
                    return self::$language = $lang;
            }
*/

            //TODO: Check from the country of origin

            //Check HTTP_ACCEPT_LANGUAGE
            $accept_languages = isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
            $accept_languages = explode(",", $accept_languages );

            foreach( $accept_languages as $accept_language ){

                //We ignore english because it is the default
                if( $accept_language == 'en' ||  $accept_language == 'en_US' )
                    continue;

                if( self::isLanguage( $accept_language ) )
                    return self::$language = $accept_language;

            }

            //Return default language
            $languages = self::getLanguages();
            $languages = array_keys( $languages );
            return self::$language = array_shift( $languages );

        }

        public static function setLanguage( $language , $persist = true )
        {

            if( !self::isLanguage( $language ) )
                return false;

            if( $persist ){
                if( $language ){
                    @setcookie( "lang", $language , time()+60*60*24*365 , '/' );
                    //Session::write( "lang" , $language );
                }else{
                    @setcookie( "lang", "" , time()- 3600 , '/' );
                    //Session::delete( "lang" );
                }
            }

            return self::$language = $language;

        }

        public static function isLanguage( $language )
        {
            return array_key_exists( $language , self::getLanguages() );
        }

        public static function isLanguageLoaded( $language = null )
        {
            $language = $language ? $language : self::getLanguage();
            return isset( self::$strings[ $language ] );
        }

        public static function load( $language = null , $force = false )
        {

            //Get the language
            if( $language === null )
                $language = self::getLanguage();

            //Check if the language is loaded
            if( isset( self::$strings[ $language ] ) && !$force ){
                return self::$strings[ $language ];
            }

            //Check if it is a valid language
            if( !self::isLanguage( $language ) )
                return new Translate_Strings();

            //Load the language
            $language_file = self::getLanguageFile( $language );
            return self::$strings[ $language ] = Translate_Format::load( $language_file );

        }

        public static function save( Translate_Strings $strings , $language = null , $force = false ){

            //Get the language
            if( $language === null )
                $language = self::getLanguage();

            //Check if the strings are modified
            if( !$strings->isModified() && !$force )
                return false;

            //Check if it is a valid language
            if( !self::isLanguage( $language ) )
                return false;

            //Save the language
            $language_file = self::getLanguageFile( $language );
            Translate_Format::save( $strings , $language_file );
            return true;

        }

        public static function loadFile( $file )
        {
            return Translate_Format::load( $file );

        }

        public static function saveFile( Translate_Strings $strings , $file )
        {
            return Translate_Format::save( $strings , $file );
        }

        public static function extract( $path = null ){

            //Initialize Arguments
            if( $path === null )
                $path = APPLICATION_PATH;

            //Get all Extractor
            $extractors = Yammon::findSubClasses('Translate_Extractor');

            //Extract strings
            $strings = new Translate_Strings();
            foreach( $extractors as $extractor ){

                $obj = new $extractor( $path );
                $newstrings   = $obj->extract( );
                $strings->merge( $newstrings );
            }

            //Save the string file
            $translation_path = Yammon::getWritablePath('translation');
            $languages_path   = Yammon::getWritablePath('translation/languages');
            $string_file      = $translation_path.'strings.php';
            Translate_Format::save( $strings , $string_file );

            //Get the languages
            $languages = self::getLanguages();

            //Update Languages
            foreach( $languages as $language => $caption ){

                //Clone original strings
                $new_language_strings = clone( $strings );

                //Load language strings
                $language_file    = self::getLanguageFile( $language );
                $language_strings = Translate_Format::load( $language_file );

                //Move the translated strings to the clone
                foreach( $language_strings as $string => $translation ){
                    $new_language_strings->set( $string , $translation );
                }

                //Save language file
                Translate_Format::save( $new_language_strings , $language_file );

            }

            //Return the strings
            return $strings;

        }


    }
