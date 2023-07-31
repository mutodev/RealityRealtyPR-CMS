<?php

    class Translate_Strings implements Iterator , ArrayAccess , Countable{

        protected $strings           = array();
        protected $modified_strings  = array();
        protected $modified          = false;


        public function __construct( $strings = array() ){
            $this->strings = (array) $strings;
        }

        public static function __set_state( $state )
        {
            $obj = new Translate_Strings;
            $obj->strings  = $state['strings'];
            $obj->modified = false;
            return $obj;
        }

        public function clear()
        {
            if( !empty( $this->strings ) ){
                $this->strings  = array();
                $this->modified = true;
            }
        }

        public function delete( $string )
        {
            if( !$this->exists( $string ) )
                return false;

            unset( $this->strings[ $string ] );
            $this->modified = true;
            return true;

        }

        public function exists( $string )
        {
            return array_key_exists( $string , $this->strings );
        }

        public function get( $string ){

            //Check if the string exists
            if( !$this->exists( $string ) )
                return null;

            //Return the string
            return $this->strings[ $string ]['translation'];

        }

        public function getLocations( $string )
        {

            //Check if the string exists
            if( !$this->exists( $string ) )
                return false;

            //Return the locations
            return $this->strings[ $string ]['locations'];

        }

        public function isModified()
        {
            return $this->modified;
        }

        public function getModified()
        {
            return array_values($this->modified_strings);
        }


        public function merge( Translate_Strings $strings )
        {

            $modified = false;
            foreach( $strings->strings as $k => $v ){

                //Check for modification
                if( !isset( $this->strings[ $k ] ) || $this->strings[ $k ] !== $v ){
                    $modified = true;
                    $this->modified_strings[ $k ] = $k;
                }

                if( !isset($this->strings[ $k ] ) ){ //Add string
                    $this->strings[ $k ] = $v;
                }else{ //Merge String

                    //Keep my translation if the other one doesn't have it
                    if( trim($v['translation']) != '' )
                        $this->strings[ $k ]['translation'] = $v['translation'];

                    //Merge Locactions
                    $new_locations = isset( $v['locations'] ) ? $v['locations'] : array();
                    for( $i = 0 ; $i < count( $new_locations ) ; $i++ ){
                        for( $j = $i + 1 ; $j < count( $new_locations ) ; $j++ ){
                            if( $new_locations[$i] == $new_locations[$j] ){
                                unset( $new_locations[$j] );
                                $new_locations = array_values( $new_locations );
                            }
                        }
                    }

                    $this->strings[ $k ]['locations'] = array_merge($this->strings[ $k ]['locations'], $new_locations);
                }

            }

            if( $modified )
                $this->modified = true;

        }

        public function set( $string , $translation ){

            //Check if the string exists
            if( !$this->exists( $string ) )
                return false;

            //Convert empty strings to
            if( trim( $translation ) == '' )
                $translation = null;

            //Set the translation
            $this->strings[ $string ][ 'translation' ] = $translation;
            $this->modified_strings[ $string ] = $string;
            $this->modified = true;
            return true;

        }

        public function toArray(){
            return $this->strings;
        }

        public function getCount(){
            return $this->count();
        }

        public function getTranslatedCount( )
        {
            static $cache = null;

            if( $cache !== null )
                return $cache;

            $count = 0;
            foreach( $this->strings as $k => $v ){
                if( $v['translation'] !== null )
                    $count++;
            }

            return $cache = $count;

        }

        public function getProgress()
        {
            $total      = $this->getCount();
            $translated = $this->getTranslatedCount();
            if( $total == 0 )
                return number_format(100,2);
            else
                return number_format(($translated/$total)*100,2);
        }

        public function matchesFilter( $key , $filters )
        {

            //Check if the strings exists
            if( !isset( $this->strings[ $key ] )){
                return false;
            }

            //If we are filtering for errors
            //we check filter for translated to
            if( isset( $filters['errors'] ) && trim($filters['errors']) != '' ){
                $filters['translated'] = 1;
            }

            //Get the string
            $string = $this->strings[ $key ];

            //Check translation positive translation
            if( isset( $filters['translated'] ) && $filters['translated'] ){
                if( $string['translation'] === null )
                    return false;
            }

            //Check translation negative translation
            if( isset( $filters['translated'] ) && !$filters['translated'] ){
                if( $string['translation'] !== null )
                    return false;
            }

            //Check for string
            if( isset( $filters['string'] ) && trim($filters['string']) ){
                $search = trim(strtolower( $key ));
                $filter = trim(strtolower( $filters['string'] ));
                if( strpos( $search , $filter ) === false )
                    return false;

            }

            //Check for translation
            if( isset( $filters['translation'] ) && trim($filters['translation']) ){
                $search = trim(strtolower( $string['translation'] ));
                $filter = trim(strtolower( $filters['translation'] ));
                if( strpos( $search , $filter ) === false )
                    return false;

            }

            //Check for possible errors
            if( isset( $filters['errors'] ) && trim($filters['errors']) != '' ){

                $t1      = new Template( $key );
                $t2      = new Template($string['translation']);
                $params1 = $t1->getParameters();
                $params2 = $t2->getParameters();
                $error = false;

                if( preg_match('/%\s+\{/', $string['translation'] ) ){
                    $error = true;
                }elseif( count( $params1 ) != count( $params2 ) ){
                    $error = true;
                }else{
                    foreach( $params1 as $k => $v ){
                        if( !in_array( $k , $params2 ) ){
                            $error = true;
                            break;
                        }
                    }
                }

                if( $filters['errors'] && !$error ){
                    return false;
                }elseif( !$filters['errors'] && $error ){
                    return false;
                }

            }

            //Check for location
            if( isset( $filters['location'] ) && trim($filters['location']) ){

                $found  = false;
                $filter = trim(strtolower( $filters['location'] ));
                foreach( $string['locations'] as $location ){
                    $search = trim(strtolower( $location[0] ));
                    if( strpos( $search , $filter ) !== false ){
                        $found = true;
                        break;
                    }
                }

                if( !$found )
                    return false;

            }

            return true;

        }

        /**
        * Iterator's Interface rewind
        */
        public function rewind()
        {
            reset($this->strings);
        }

       /**
        * Iterator's Interface current
        */
        public function current()
        {
            $current = current( $this->strings );
            return $current && isset( $current['translation'] ) ? $current['translation'] : null;
        }

       /**
        * Iterator's Interface key
        */
        public function key()
        {
            return key($this->strings);
        }

       /**
        * Iterator's Interface next
        */
        public function next()
        {
            return next( $this->strings );
        }

       /**
        * Iterator's Interface valid
        */
        public function valid()
        {
            return key($this->strings) !== null;
        }

       /**
        * Countable Interface count
        */
        public function count()
        {
            return count( $this->strings );
        }

       /**
        * ArrayAccess Interface offsetExists
        */
        public function offsetExists( $key )
        {
            return $this->exists( $key );
        }

       /**
        * ArrayAccess Interface offsetGet
        */
        public function offsetGet( $key )
        {
            return $this->get( $key );
        }

       /**
        * ArrayAccess Interface offsetSet
        */
        public function offsetSet( $key, $value )
        {
            return $this->set( $key , $value );
        }

       /**
        * ArrayAccess Interface offsetUnset
        */
        public function offsetUnset( $key )
        {
            return $this->delete( $key );
        }

    }
