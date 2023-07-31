<?php

    abstract class Translate_Extractor{

        private $path = null;

        public function __construct( $path )
        {
            $this->path = $path;
        }

        protected function check( $file )
        {

            //Check that the file exists
            if( !FS::isFile( $file ) )
                return null;

            //Get the cache file
            $cache_file = $this->getCacheFileFor( $file );

            //Get Modified Times
            $cache_file_modified = FS::mtime( $cache_file );
            $file_modified       = FS::mtime( $file );

            //If the file hasn't been modified return it
            $return = null;
            if( $file_modified <= $cache_file_modified ){
                $return = Translate_Format_Php::load( $cache_file );
            }

            return $return;

        }

        public function extract(  )
        {

            //Create new strings
            $strings = new Translate_Strings();

            //Get the list of files to extract from
            $files       = $this->getFiles();

            //Loop thru the files
            foreach( $files as $file ){

                //Check the cache
                if( ($cache = $this->check( $file )) !== null ){
                    $strings->merge( $cache );
                    continue;
                }

                //Do the actual extraction
                $extraction  = (array)$this->extractFile( $file );
                $filestrings = new Translate_Strings( $extraction );

                //Store Extraction in cache
                $this->store( $file , $filestrings );

                //Merge strings
                $strings->merge( $filestrings );

            }

            return $strings;
        }


        abstract protected function extractFile( $file );

        protected function getCacheFileFor( $file )
        {
            $md5  = md5( get_class($this).$file );
            $path = str_split($md5 , 4 );
            $file = array_pop( $path );
            $path = implode( DS , $path );
            $root = Yammon::getTemporaryPath("translation".DS.$path );
            return $root.$file;
        }

        abstract public function getFiles();

        public function getPath(  )
        {
            return $this->path;
        }

        protected function store( $file , Translate_Strings $strings )
        {

            //Check that the file exists
            if( !FS::isFile( $file ) )
                return false;

            //Get the cache file
            $cache_file = $this->getCacheFileFor( $file );

            //Get Modified Time
            $file_modified = FS::mtime( $file );

            //Save Contents
            Translate_Format_Php::save( $strings , $cache_file );

            //Make sure the modified times match
            touch( $cache_file , $file_modified );

            return true;

        }

    }
