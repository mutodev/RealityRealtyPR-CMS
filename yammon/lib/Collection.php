<?php
	
/**
*   Collection Class
*
*   Base collection class. Its basically a proxy to an array with some goodies.
*   It implements iterator interface so it can be used with foreach. 
*   It implements the ArrayAccess interface so the collection behaves like an array and 
*   It implements Countable so you can use count function on it.
*   It also implements the set and get magic methods so it behaved like an object.
*   And lastly it has support to make the collection readonly.
*   It required php5 to work
*
*  @author Mon Villalon
*  @date 2009
*
*/
		
    class Collection implements Iterator , ArrayAccess , Countable{
		
        private $elements         = array();
        private $separator        = ".";
        private $readonly         = false;
        private $case_insensitive = false;
        
        private $filter           = null;
        private $sort             = null;
        private $sort_asc         = true;
        
        
       /**
        * Constructor for the collection
        * 
		* @param elements an array of elements
		* @param readonly sets whether the collection can be edited
        */		
	    public function __construct( $elements = null , $readonly = false  , $case_insensitive = false ){

            $this->case_insensitive = $case_insensitive;
            
            if( $elements !== null ){ 
                $this->fromArray( $elements );
            }
            
            $this->readonly = $readonly;
            
        }

       /**
        * Returns if the collection is readonly
        */
        public function readonly(){
            return (bool) $this->readonly;
        }

       /**
        * Returns if the collection is case_insensive
        */
        public function caseInsensitive(){
            return (bool) $this->case_insensitive;
        }



       /**
        * Get an element from the collection by key
        */
        public function get( $key , $default = null ){
                
            if( $this->case_insensitive )
                $key = strtolower( $key );

	        if( !isset( $this->elements[$key] ) ){
		        return $default;
            }else{
		        return $this->elements[ $key ];
            }
                       
        }
        
       /**
        * Set an element from the collection by key
        */
        public function set( $key , $value = null ){
    
            if( $this->case_insensitive )
                $key = strtolower( $key );
    
            if( $this->readonly )
                throw new Collection_ReadOnlyException("Can't set '$key' the collection is readonly");

            //If keys is null append to the last of the array
            if( $key === null ){

                $this->elements[ ] = $value;
                return $value;
                
            }elseif( is_array( $key ) ){

                foreach( $key as $k => $v )
                    $this->elements[ $k ] = $v;
                    
                return $key;
                
            }else{
            
                $this->elements[ $key ] = $value;
                return $value;
                
            }
            
        }

        public function merge( $collection , $prefer_new = true ){
        
            if( $this->readonly )
                throw new Collection_ReadOnlyException("Can't set '$key' the collection is readonly");
        
            if( empty( $collection ) )
                return;
        
            if( !is_array( $collection ) )
                if( $collection instanceof Collection )
                    $collection = $collection->toArray();
                else
                    $collection = (array) $collection;
                
            //Merge the elements
            if( $prefer_new )
                $elements = array_merge( $this->elements , $collection );
            else
                $elements = array_merge( $collection , $this->elements );
            
            $this->fromArray( $elements );
            
        }
        
        public function extract( $key , $default = null , $separator = null ){
            return $this->_extract( $this->elements , $key , $default , $separator );
        }
        
        protected function _extract( $input , $key , $default = null , $separator = null ){

            if( empty( $separator ) )
                $separator = $this->separator;

            if( !is_array( $key ) )
                $path  = explode( $separator , $key );
            else
                $path  = $key;
    
            $key   = array_pop( $path );       
            $array = $input;
                                    
            foreach( $path as $p ){
                    
                if( isset( $array[ $p ] ) )
                    $array = $array[ $p ];
                else
                    return $default;
    
            }
            
            if( !is_array( $array ) ){
                return $default;
            }elseif( !isset( $array[$key] ) ) {
                return $default;
            }
                
            return $array[$key];

        }
             
       /**
        * Checks if there exists an element if the collection with the specified key
        */
        public function exists( $key ){

            if( $this->case_insensitive )
                $key = strtolower( $key );
      
            return array_key_exists( $key , $this->elements );
               
        }

       /**
        * Delete the element in the collection that contains the specified key
        */
        public function delete( $key ){

            if( $this->case_insensitive )
                $key = strtolower( $key );

            if( $this->readonly )
                throw new Collection_ReadOnlyException("Can't set '$key' the collection is readonly");
                
            unset( $this->elements[ $key ] );
            return true;
        }

       /**
        * Removes all elements from the collection
        */
        public function clear( ){

            if( $this->readonly )
                throw new Collection_ReadOnlyException("Can't set '$key' the collection is readonly");
                
            $this->elements = array();
            return true;
        }

       /**
        * Loads the information from an array
        */
        public function fromArray( $array ){                

            if( $this->readonly )
                throw new Collection_ReadOnlyException("Can't set '$key' the collection is readonly");

            if( !is_array( $array ) ){
                $array = (array)$array;
            }

            if( $this->case_insensitive ){
                $newarray = array();
                foreach( $array as $key => $value ){
                    $newarray[ strtolower( $key) ] = $value;
                }
                $array = $newarray;
            }
            $this->elements = $array;
                        
        } 

       /**
        * Flattens the array
        */
        public function flatten( $data = null ){

            if( isset( $this ) && $this instanceof Collection ){
                $data = $this->toArray();
            }elseif( $data instanceof Collection ){
                $data = $data->toArray();                
            }else{
                $data = (array) $data;
            }

            $flattened  = self::_flatten( $data , '.'  , '' );
            $Collection = new Collection();
            $Collection->fromArray( $flattened );            
            return $Collection;
            
        }
                
        protected static function _flatten( $array , $separator  , $prefix ){

            $return = array();
                
            if( $prefix != "" )
                $prefix = $prefix . $separator;
                                                
            foreach( $array as $key => $value ){

                $subkey = $prefix.$key; 
                if( is_array( $value ) ){
                    $subvalues = self::_flatten( $value , $separator , $subkey );
                    $return    = array_merge( $return , $subvalues );
                }else{
                    $return[ $subkey ] = $value;                
                }

            }
            
            return $return;        
        
        }
        
       /**
        * Unflattens the array
        */        
        public function unflatten( $data = null ){
       
            if( isset( $this ) && $this instanceof Collection ){
                $data = $this->toArray();
            }elseif( $data instanceof Collection ){
                $data = $data->toArray();                
            }else{
                $data = (array) $data;
            }

            $unflattened = self::_unflatten( $data , '.' );
            $Collection = new Collection();
            $Collection->fromArray( $unflattened );            
            return $Collection;            
        
        }
        
        protected static function _unflatten( $array , $separator ){
                    
            $unflattened = array();

            foreach( $array as $key => $value ){

                $sub_return = &$unflattened;               
                $path       = explode( $separator , $key );
                $last       = array_pop( $path );
                
                foreach( $path as $p ){
                    if( !isset( $sub_return[ $p ] ) )
                        $sub_return[ $p ] = array();
                    elseif( !is_array(  $sub_return[ $p ] ) )
                        $sub_return[ $p ] = (array) $sub_return[ $p ];                    

                    $sub_return = &$sub_return[ $p ];
                }
            
                if( !isset( $sub_return[ $last ] ) )
                    $sub_return[ $last ] = $value;
                elseif( !is_array(  $sub_return[ $last ] ) ){
                    $sub_return[ $last ]   = (array) $sub_return[ $last ];                    
                    $sub_return[ $last ][] = $value;                    
                }else{
                    $sub_return[ $last ][] = $value;                                    
                }
            
            
            }    
            
            return $unflattened; 
        
        }
        

        /** 
         * Filter the Collection by a value
         * TODO: Accept Search Strings
         */  
        public function filter( $filter ){

            //Make sure we have elements to filter
            if( !count( $this->elements) )
                return new Collection();
                
            //Cast Filter to array                
            if( !is_array( $filter) ){
                $filter_value = $filter;
                $filter       = array();                
                $first        = $this->elements[0];
                $keys         = array_keys( $first );
                foreach( $keys as $key ){
                    $filter[ $key ] = $filter_value; 
                } 
            }
                        
            $this->filter = $filter;
        
            $filtered = array_filter( $this->elements , array( $this , "_filter") );
            $Collection = new Collection();
            $Collection->fromArray( $filtered );
            return $Collection;
        
        }

        protected function _filter( $row ){

            $matches = false;

            foreach( $this->filter as $filter_key => $filter_value ){
            
                //Normalize value
                $filter_value = strtoupper( trim( $filter_value ) );
            
                //Don't filter empty values
                if( $filter_value == '' )
                    continue;
            
                //Get the Current Value
                $current_value = strtoupper(trim( $this->_extract( $row , $filter_key ) ));
                
                //Check if it matches
                if( strpos( $current_value , $filter_value ) !== false ){
                    $matches = true;
                }
                
            }

            return $matches;

        }
      
        /** 
         * Sort Collection by key
         * TODO: Specify sorts
         */        
        public function sort( $key , $asc = true ){      
                
            $this->sort = $key;                
            $this->sort_asc = $asc;
            
            $sorted = $this->elements;
            usort( $sorted , array( $this , "_sort") );
            $Collection = new Collection();
            $Collection->fromArray( $sorted );
            return $Collection;        
        
        }
      
        protected function _sort( $a , $b ){
            $value1 = (string) $this->_extract( $a , $this->sort );
            $value2 = (string) $this->_extract( $b , $this->sort );            
                        
            $comp = strnatcmp( $value1 , $value2 );

            if( $this->sort_asc )
                return $comp;
            else
                return $comp*-1;

        }

       /**
        * Returns the array representation of the collection
        */
        public function toArray( ){                
            return $this->elements;
        }        

       /**
        * magic get method
        * 
		* Makes it possible to use the collection
		* as an object
		*
		* @param key the element to be accessed
		* @return the value of the element associated with the \akey 
        */
	    public function __get( $key ){
	        return $this->get( $key );
        }

       /**
        * magic set method
        * 
        * Gets called when an attribute is set. Throws an exception when the element
        * as an object
        *
        * @param key   the element to be setted
        * @param value the value to set
        * @return the value of the element associated with the \akey 
        */
        public function __set( $key , $value ){
	        return $this->set( $key , $value );
        }
    
       /**
        * Magic isset Method
        * 
        * Gets called when isset is used on the collection
        * as an object
        *
        * @param key the element to be checked
        * @return boolean determines if the key exists in the collection
        */
        public function __isset( $key ){
            return isset(  $this->elements[ $key ] );
        }
    
       /**
        * Magic unset Method
        * 
        * Gets called when unset is used on the collection
        * as an object. Throws an exception if the collection
        * is read only.
        *
        * @param key the element to be checked
        */
        public function __unset( $key ){
            $this->delete( $key );
        }

       /**
        * Magic __set_state Method
        * 
        * Gets called when var_export is used on the collection
        * as an object. 
        *
        * @param  state and array with all class attributes
        * @return a new collection
        */
        public function __set_state( $state ){
            $obj = new A;
            $obj->elements = $state['elements'];
            $obj->readonly = $state['readonly'];
            $obj->strict   = $state['strict'  ];	
            return $obj;
        }
        
       /**
        * Iterator's Interface rewind
        */
        public function rewind() {
            reset($this->elements);
        }

       /**
        * Iterator's Interface current
        */
        public function current() {
            return current($this->elements);
        }

       /**
        * Iterator's Interface key
        */
        public function key() {
            return key($this->elements);
        }

       /**
        * Iterator's Interface next
        */
        public function next() {
            return next( $this->elements );
        }

       /**
        * Iterator's Interface valid
        */
        public function valid() {
            return key($this->elements) !== null;
        }

       /**
        * Countable Interface count
        */
        public function count(){
            return count( $this->elements );
        }

       /**
        * ArrayAccess Interface offsetExists
        */
        public function offsetExists( $key ){
            return $this->exists( $key );
        }
	
       /**
        * ArrayAccess Interface offsetGet
        */	
        public function offsetGet( $key ){
            return $this->get( $key );
        }	
	
       /**
        * ArrayAccess Interface offsetSet
        */	
        public function offsetSet( $key, $value ){
            return $this->set( $key , $value );            
        }	
	
       /**
        * ArrayAccess Interface offsetUnset
        */	
        public function offsetUnset( $key ){
            return $this->delete( $key );
        }	

       /**
        * Call function callback on all elements of the collection
        */				
        public function invoke( $function_name , $arguments = null ){
            $arguments      = func_get_args();
            $function_name  = array_shift( $arguments );
            return $this->invokeArray( $function_name , $arguments );
        }  

       /**
        * Variant of invoke but arguments are passed as an array
        */				
        public function invokeArray( $callback , $arguments = array() ){
        
            foreach( $this->elements as $element )
                if( method_exists( $element , $callback ) )
                    call_user_func_array( array( &$element , $callback ) , $arguments );
        
            return $this;
            
        }  
			
}
