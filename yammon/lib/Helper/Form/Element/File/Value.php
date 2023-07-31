<?php

/**
 *
 * @method string getOriginalName()
 * @method string getUploadedData()
 * @method string getMime()
 * @method string getSize()
 * @method string getStatus()
 * @method string setStatus() setStatus(string $status)
 *
 */

    class Helper_Form_Element_File_Value {

		protected $path       = '';
        protected $properties = array();

		public function __construct( $path ){

			$this->path = $path;
            $this->setDefaultProperties();
		}

		public function __toString(){
			return $this->getPath() ? $this->getPath() : '';
		}

		public function getExtension(){
            return pathinfo($this->path, PATHINFO_EXTENSION);
		}

		public function getPath(){
			return $this->path;
		}

        public function isEmpty(){
            return ( $this->path );
        }

		public function save( $location ){

            $FileSystem = Configure::read("filesystem");

            if (!$this->getProperty('is_new')) {
                return true;
            }

            return $this->moveFile( $location );
		}

        public function setProperties( $properties ){

            //The foreach is to maintain the default properties
            foreach ( $properties as $key => $value) {
                $this->setProperty($key, $value );
            }
        }

        public function getProperties( ){
            return $this->properties;
        }

        public function setProperty( $key, $value ) {
            $this->properties[$key] = $value;
        }

        public function getProperty( $key ) {
            return isset( $this->properties[$key] ) ? $this->properties[$key] : null;
        }

        public function hasProperty( $key ) {
            return isset( $this->properties[$key] );
        }

        public function delete() {

            $FileSystem = Configure::read("filesystem");

            return $FileSystem->delete($this->getPath());
        }

        protected function moveFile( $location ) {

            $FileSystem = Configure::read("filesystem");

            $newLocation = $this->saveLocationAdditionalPath($location);
            $newPath     = $newLocation . DS . $this->generateUniqueFilename();

            //Make sure the folder exists
            $FileSystem->createDir($newLocation);


            if ( $FileSystem->rename($this->getPath(), $newPath) ) {
                $this->path = $newPath;
                $this->setProperty('status', 'ACTIVE');

                return true;
            }

            return false;
        }

        protected function setDefaultProperties( ) {

            $FileSystem = Configure::read("filesystem");

            $this->setProperty('original_name', basename( $this->path ) );
            $this->setProperty('uploaded_data', time()                  );
            $this->setProperty('mime'         , $FileSystem->getMimetype( $this->path ) );
            $this->setProperty('size'         , $FileSystem->getSize( $this->path ) );
            $this->setProperty('status'       , 'INACTIVE' );
            $this->setProperty('is_new'       , true );
        }

        protected function saveLocationAdditionalPath( $location ) {
            return $location . DS . date('Y/m/d', $this->getProperty('uploaded_data') );
        }

        protected function generateUniqueFilename() {
            $time = round(microtime(true) * 1000)  - (strtotime('today 00:00:00') * 1000);
            return $time . '_' . $this->getOriginalName();
        }

        public function __call($name, $arguments) {

            if ( ($method = substr($name, 0, 3)) && strtoupper($name{3}) === $name{3} ) {

                $propertyName = preg_replace('/(?<=\\w)(?=[A-Z])/',"_$1", substr($name, 3));
                $propertyName = strtolower( $propertyName );

                if ( $method == 'get' && $this->hasProperty( $propertyName ) )
                    return $this->getProperty( $propertyName );

                if ( $method == 'set' && isset($arguments[0]) )
                    return $this->setProperty( $propertyName, $arguments[0] );

            }

            trigger_error(sprintf('Call to undefined function: %s::%s().', get_class($this), $name), E_USER_ERROR);
        }

    }
