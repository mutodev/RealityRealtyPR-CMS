<?php

    class ListMax_Core_Collection_Item implements Countable,Iterator,ArrayAccess{

		protected $data;

		/* -- Constructors -------------------------------- */
		public function __construct( $data ){
		    $this->orginalData = $data;
			$this->data        = $data;
        }        
		/* -- Magic Methods ------------------------------- */
		public function __isset( $key ){

			if( method_exists( $this , "field_$key" ) ){
			    return true;
            }else{
			    return isset( $this->data[ $key ]);
            }
            
		}
		/* ------------------------------------------------ */
		public function __get( $key ){

			if( method_exists( $this , "field_$key" ) ){
			    return $this->{"field_$key"}();
			}elseif( array_key_exists( $key , $this->data ) ){
				return $this->data[ $key ];
			}else{
				trigger_error( "$key is not a property"  , E_USER_WARNING );			
			}
		
		}

        /* -- Countable Interface ------------------------- */
		public function count(){
			return count( $this->data );
		}

        /* -- Array Access Interface ---------------------- */
        public function offsetExists($key) {
        
			if( method_exists( $this , "field_$key" ) ){
                return true;        
            }else{
                return isset($this->data[$key]);
            }
            
        }
		/* ------------------------------------------------ */
        public function offsetGet( $key ) {

            if( $this->offsetExists( $key ) )
				return $this->data[ $key ];
			else
				trigger_error( "$key is not a property"  , E_WARNING );
        
        }
		/* ------------------------------------------------ */
        public function offsetSet($key, $value) {        
                
        }
		/* ------------------------------------------------ */
        public function offsetUnset( $key ) {

        }
        
		/* -- Iterator Methods ---------------------------- */
		public function rewind() {
			reset($this->data);
		}
		/* ------------------------------------------------ */
		public function current() {
			$var = current($this->data);
			return $var;
		}
		/* ------------------------------------------------ */
		public function key() {
			$var = key($this->data);
			return $var;
		}
		/* ------------------------------------------------ */
		public function next() {
			$var = next($this->data);
			return $var;
		}
		/* ------------------------------------------------ */
		public function valid() {
			$var = $this->current() !== false;
			return $var;
		}
		
		public function toArray(){
		    return $this->data;
		}
		
	}
