<?php

    class ListMax_Core_Collection_Item_Image{

        private $image_namespace;
        private $id;
        private $index;
        private $width;
        private $height;
        private $quality;
                        
        /* --------------------------------------------------------- */    
        public function __construct( $image_namespace , $id , $index , $width = null , $height = null , $quality = 90 ){

            $this->image_namespace = $image_namespace;
            $this->id              = $id;
            $this->index           = $index;
            $this->width           = $width;
            $this->height          = $height;
            $this->quality         = $quality;
            
        }
        /* --------------------------------------------------------- */
        public function __toString(){
            return $this->getPath( $this->width , $this->height , $this->quality );
        }        
        /* --------------------------------------------------------- */
        public function get( $width = null , $height = null , $quality = 90 , $options = array() ){
            return $this->getPath( $width , $height , $quality , $options );
        }
        /* --------------------------------------------------------- */
        private function getPath( $width = null , $height = null , $quality = 90 , $options = array() ){

            $path   = array();
            $path[] = "http://images.listmax.com";         
            $path[] = $this->image_namespace;
               
            if( $width != null ){
                $options['w'] = $width;
            }

            if( $height != null ){
                $options['h'] = $height;            
            }

            if( $quality != null ){
                $options['q'] = $quality;                       
            }
               
            foreach( $options as $k => $v ){
                $path[] = $k.':'.$v;
            }
               
            for( $i = 0 ; $i < strlen( $this->id ) ; $i++ ){
                $path[] = substr( $this->id , $i , 1 );
            }            
            $path[] = $this->index.".jpg";

            return implode( "/" , $path );

        }
        /* --------------------------------------------------------- */
                
    }

?>
