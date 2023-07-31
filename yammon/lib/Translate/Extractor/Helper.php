<?php

    abstract class Translate_Extractor_Helper extends Translate_Extractor{

        protected $helper = null;

        public function getFiles()
        {
            $helper  = strtolower( $this->helper );
            $path    = $this->getPath();
            $files1  = FS::findFiles( $helper.".yml"   , true , $path );
            $files2  = FS::findFiles( "*.".$helper.".yml" , true , $path );
            $files   = array_merge( $files1 , $files2 );  
                                                
            return $files;
        }

        public function extractFile( $file )
        {
                                                        
            //Get the strings
            try{
                $options = Yaml::load( $file );            
                $class  = "Helper_".$this->helper;
                $object = new $class( $this->helper , $options );

                //Get Strings
                $strings = $object->getTranslationStrings();
                
                //Trasnform strings
                $return = array();
                foreach( $strings as $string ){
                    $return[ $string ]['translation'] = null;
                    $return[ $string ]['locations'][] = array( $file , null );
                }
                
                //Return
                return $return;
                
                
            }catch( Exception $ex ){
                return null;
            }
        
        }

    }