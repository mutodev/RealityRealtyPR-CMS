<?php

	class Layout extends View{

        protected $layout  = null;
        protected $content = null;

        public function setLayout( $layout ){
            $this->layout = $layout;
        }

        public function getLayout(){
            return $this->layout;
        }

        public static function isLayout( $layout ){
            $l = new Layout();
            $l->setLayout( $layout );
            $file = $l->getFile();
            return !!$file;
        }

        public function getFile(){

            if( !empty( $this->file) )
                return $this->file;

            $layout      = $this->layout;
            $basename    = $layout ? $layout.".phtml" : "default.phtml";
            $base_layout = Configure::read('layout');

            //Get the layout file
            $layout_file  = null;
            $paths        = Yammon::getLayoutsPaths();

            foreach( $paths as $path ){
                $files   = array();
                if( $base_layout ) $files[] = $path.$base_layout."/".$basename;
                $files[] = $path . $basename;

                foreach( $files as $file ){
                    if( FS::isFile( $file ) ){
                        $layout_file = $file;
                        break(2);
                    }
                }

            }

            return $layout_file;

        }

		public function getContent(){
		    return $this->content;
		}

        public function setContent( $content ){
            return $this->content = $content;
        }

        public function render( $file = null ){
            return parent::render( $file );
        }

	}

