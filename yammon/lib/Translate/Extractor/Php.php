<?php

    class Translate_Extractor_Php extends Translate_Extractor{

        protected $index     = -1;
        protected $tokens    = array();
        protected $line      = 0;

        protected function eof()
        {
            return $this->index >= count( $this->tokens );
        }

        protected function next( )
        {
            $this->index++;
            $this->whitespace();
        }

        protected function tokenid()
        {

            if( $this->eof() )
                return null;

            if( is_array( $this->tokens[ $this->index ] ) )
                return $this->tokens[ $this->index ][0];
            else
                return null;

        }

        protected function tokenval()
        {

            if( $this->eof() )
                return null;

            if( is_array( $this->tokens[ $this->index ] ) )
                return $this->tokens[ $this->index ][1];
            else
                return $this->tokens[ $this->index ];

        }

        protected function tokenline()
        {

            if( $this->eof() )
                return null;

            if( is_array( $this->tokens[ $this->index ] ) )
                return $this->line = $this->tokens[ $this->index ][2];
            else
                return $this->line;

        }

        protected function whitespace()
        {
            while( ($token_id = $this->tokenid()) === T_WHITESPACE )
                if( $this->eof() )
                    return;
                else
                    $this->index++;
        }
        
        public function getFiles()
        {
            $path = $this->getPath();
            $files1  = FS::findFiles("*.php"   , true , $path );
            $files2  = FS::findFiles("*.phtml" , true , $path );            
            return array_merge( $files1 , $files2 );                    
        }
        
        protected function extractFile( $file  )
        {        
                    
            $strings = array();

            //Reset Variables
            $this->index     = -1;
            $this->tokens    = array();
            $this->line      = 0;

            //Load Tokens
            $contents      = file_get_contents( $file );
            @$this->tokens = token_get_all( $contents );

            //Extract
            while( !$this->eof() ){

                $this->next();
                while( true  ){

                    if( $this->eof() )
                        break 2;

                    if( $this->tokenid() == T_STRING && $this->tokenval() == "t" )
                        break;

                    $this->next();
                }

                $this->next();
                if( $this->tokenid() !== null || $this->tokenval() != "(" ){
                    continue;
                }

                $this->next();
                if( $this->tokenid() !== T_CONSTANT_ENCAPSED_STRING ){
                    continue;
                }

                $line   = $this->tokenline();
                $string = $this->tokenval();
                $string = substr( $string , 1 , -1 );
                $strings[ $string ]['translation'] = null;
                $strings[ $string ]['locations'][] = array( $file , $line );

            }                        

            return $strings;

        }

    }