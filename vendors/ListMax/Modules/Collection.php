<?php

    class ListMax_Collection implements Countable, Iterator, ArrayAccess {

        protected $count;
        protected $data;
        protected $fetched;
        protected $filters          = array();
        protected $filterValues;
        protected $itemClass        = "ListMax_CollectionItem";
        protected $orders;
        protected $orderedDirection = array();
        protected $orderedBy        = array();
        protected $page             = null;
        protected $pageSize         = null;
        protected $primaryKey       = "id";
        protected $tableName        = "collection";
        protected $ids              = array();
                
        /* -- Constructors -------------------------------- */
		public function __construct( ){		
            $this->reset();
		}

        /* -- Helper Methods ------------------------------ */
		private function primary(){
			return empty($this->tableName) ? $this->primaryKey : $this->tableName.".".$this->primaryKey;
		}
        /* ------------------------------------------------ */		
		private function getItem( &$data ){
	    	
            $appclass = "App".$this->itemClass; 
            if( class_exists( $appclass , false ) ){
                return new $appclass( $data );                        
            }else{
                return new $module( $data );
            }
		
		}
        /* -- Public Methods ------------------------------ */
        public function fetch( $cache = "" ){
            
            global $ListMax;
            
            if( $this->fetched ){ 
                return;
            }
            
            prd(11);
            $select = $this->getSelect();
            echo (string)$select;exit();
            $this->fetched = true;
            $this->filter( $select );
            $this->order( $select );
            $this->paginate( $select );
            
			if( !empty($this->ids) ){
    			$select->where( $this->primary() . ' IN ('. implode( ',' , $this->ids ).")" );
                $this->data = $ListMax->query( $select , $this->tableName , $cache );
            }
                                                            
            return $this->data = $ListMax->query( $select , $this->tableName , $cache );
                        
        }
        /* ------------------------------------------------ */
        public function fetchIds( $cache = "" ){
 
            global $ListMax;
 
            $result = array();
                
            if( empty( $this->page ) && empty( $this->orderedBy ) ){

                $select = $this->getSelect();
			    $select->reset( Zend_Db_Select::COLUMNS );
                $this->filter( $select );
                
			    $select = 'SELECT '.$this->primary().' '.$select->__toString();
                $result = $ListMax->query( $select , $this->tableName  , $cache );

            }else{
                $result = $this->fetch();                            
            }

            $ids = array();
            foreach( $result as $row ){
                $ids[] = $row[ $this->primaryKey ];
            }

            return $ids;
            
        }        
        /* -------------------------------------------------- */
        public function fetchById( $id , $cache = "" ){
                        
            global $ListMax;
                        
            $select = $this->getSelect();
			$pk     = empty($this->tableName) ? $this->primaryKey : $this->tableName.".".$this->primaryKey;
            $select->where("$pk = ?" , $id );
            $this->filter( $select );
                        
            $result = $ListMax->query( $select , $this->tableName , $cache );
                                                                   
            if( empty( $result ) )
                return null;
            else
    			return $this->getItem( current($result) );
                        
        }
        /* -------------------------------------------------- */
        public function fetchByIds( $ids ){
                        
			if( empty($ids)){
				$ids = array(-1);
			}
			
            $this->fetched = false;
            $this->ids     = $ids;
            $this->fetch();
            			
        }
        /* -------------------------------------------------- */
        public function at( $key ){
            $this->fetch();
            
            if( !isset( $this->data[ $key ] ) )
                return null;
            else
    			return $this->getItem( $this->data[ $key ] );
    			
        }
        /* ------------------------------------------------ */
        public function filterBy( $key , $value = null ){

            //Invalidate Data
            $this->fetched = false;

            if( !is_array( $key ) )
                $key = array( $key => $value );

            $filters =  $this->filters ;

            foreach( $key as $k => $v )
                if( !in_array( $k , $filters  ) || trim($v) == '' )
                    continue;
                else
                    $this->filterValues[ $k ] = $v;
                                        
            return $key;
        
        }
        /* ------------------------------------------------ */
        public function removeFilterBy( $key ){
              
              $key = strtolower( trim( $key ) );
                
              if( isset( $this->filterValues[ $key ] ) ){
                  $this->fetched = false;
                  unset( $this->filterValues[ $key ] );
              }
              
        }
        /* ------------------------------------------------ */
        public function getFilters( ){
        
            $return  = array();
            $filters = $this->filters;
            
            foreach( $filters as $filter )
                if( $this->isFilteredBy( $filter ) )
                    $return[ $filter ] = $this->getFilterBy( $filter );
            
            return $return;
        }
        /* ------------------------------------------------ */
        public function getFilterBy( $key , $default = null ){

            $key = strtolower( trim( $key ) );
            
            if( !isset( $this->filterValues[ $key ] ))
                return $default;

            $value = $this->filterValues[ $key ];

			if( method_exists( $this , "normalize_$key" ) ){
			    return $this->{"normalize_$key"}( $value );
            }else{
                return $value;
            }
            
        }
        /* ------------------------------------------------ */
        public function isFilteredBy( $key ){

            $key   = strtolower( trim( $key ) );
            $value = $this->getFilterBy( $key );

            if( $value === null )
                return false;
            
			$value = trim( $value );
			
			if( $value === '0' )
                return true;
				
            if( $value === 0 )
                return true;

            return !empty( $value );
                
        }
        /* ------------------------------------------------ */
        public function orderBy( $key , $direction = 1 ){

            //Invalidate Data
            $this->fetched = false;

            if( ! isset( $this->orders[ $key]  ) )
                return;

            $this->orderedBy[]        = $key;
            $this->orderedDirection[] = !!$direction;
        
        }
        /* ------------------------------------------------ */
        public function isOrderedBy( $key , $dir , $position = 0){

            if( count( $this->orderedBy ) == 0 )
                return false;
                
            @$orderBy  = $this->orderedBy[ $position ];
            @$orderDir = $this->orderedDirection[ $position ];
                
            return $orderBy == $key && $orderDir == !!$dir;
        }        
        /* ------------------------------------------------ */
        public function page( $page = null , $size = null ){

            //Invalidate Data
            $this->fetched = false;
            $this->page    = $page;
            
            if( $size !== null ) 
                $this->pageSize( $size );
                            
        }
        /* ------------------------------------------------ */
        public function getPage(){
            return $this->page;
        }
        /* ------------------------------------------------ */
        public function pageSize( $size = null ){
        
            //Invalidate Data
            $this->fetched = false;
            $this->pageSize = $size;
        }
        /* ------------------------------------------------ */
        public function getPageSize(){
            return $this->pageSize;
        }
        /* ------------------------------------------------ */
        public function pageCount( $cache = "" ){
            
            $pageSize     = $this->pageSize;
            $unpagedCount = $this->unpagedCount( $cache );
            
            if( $pageSize == null )
                return 1;
            else
                return ceil($unpagedCount / $pageSize);
            
        }
        /* ------------------------------------------------ */
        public function unpagedCount( $cache = "" ){
                        
            global $ListMax;
                        
            //Check if we already computed it
            if( $this->count !== null ){
                return $this->count;
            }
            
        	//Create Filtered Select Statement
			$select  = $this->getSelect();
            $this->filter( $select );
			
			//Add ids
			if( !empty($this->ids) ){
    			$select->where( $this->primary() . ' IN ('. implode( ',' , $this->ids ).")" );
            }
			
			//Check if the query is grouped
			$groupPart = $select->getPart( Zend_Db_Select::GROUP );
			$isGrouped = !empty($groupPart);

			//Convert into count query
            if( !$isGrouped ){
    			$select->reset( Zend_Db_Select::COLUMNS );
    			$select = "SELECT count(*) as count ".$select->__toString();
	    	}else{
    			$select = "SELECT count(*) FROM (".$select->__toString().") AS COUNT_TABLE";
	    	}

			//Execute and cache
            $result = $ListMax->query( $select  , $this->tableName , $cache );
			$row    = current( $result );
			$field  = current( $row );
			$this->count = $field;
			return $this->count;
			        
        }

        /* ------------------------------------------------ */           
        public function reset(){
                
            $this->count            = null;
            $this->data             = array();
            $this->fetched          = false;
            $this->filterValues     = array();
            $this->orderedBy        = array();
            $this->orderedDirection = array();
            $this->page             = null;
            $this->pageSize         = null;
                
        }

        /* -- Protected Methods --------------------------- */
        protected function getSelect(){
            $db    = ListMax::db();
            $select = $db->select();
            $select->from( $this->tableName );
            return $select;
        }
        /* ------------------------------------------------ */
        protected function filter( &$select ){

        }           
        /* ------------------------------------------------ */
        protected function order( &$select ){
                                          
            if( !empty($this->ids) && empty($this->orderedBy) ){
                $select->order( new Zend_DB_Expr("FIND_IN_SET( ". $this->primary()." , '".implode( ',' , $this->ids )."')"));
                return;            
            }elseif( empty($this->orderedBy) ){            
                return;
            }
                                                                        
            for( $i = 0 ; $i < count( $this->orderedBy ) ; $i++ ){

                $orderBy   = $this->orderedBy[ $i ];
                if( $orderBy == "rand" ){
                    $select->order( new Zend_DB_Expr( "RAND()" ) );
                }else{
                    $fields    = $this->orders[ $orderBy ];
                    $direction = (!!$this->orderedDirection[ $i ]) ? "ASC" : "DESC";                
                    
                    if( !is_array($fields) )
                        $fields = array( $fields );
                                
                    foreach( $fields as $key => $f ){
                        $select->order( "$f $direction" );
                    }
                    
                }
                
            }
                                                
        }        
        /* ------------------------------------------------ */
        protected function paginate( &$select ){
                                    
            if( $this->page === null || $this->pageSize <= 0 )
                return;
          
            //Get Page Count
			$pagecount = $this->pageCount();
            
            //Adjust page
			if( $pagecount == 0 ){
			     $this->page = 0;
			}elseif( $this->page >= $pagecount ){
			     $this->page = $pagecount - 1;
			}elseif($this->page <= 0 ){
			     $this->page = 0;
			}

			//Add Limit Clause to select
			$select->limit( $this->pageSize , $this->page * $this->pageSize );

        }     
                                           	
        /* -- Magic Methods ------------------------------- */
		public function __isset( $key ){
    		$this->fetch();
			if( method_exists( $this , "field_$key" ) ){
			    return true;
            }else{
                return isset($this->data[$key]);
            }
            
		}
		/* ------------------------------------------------ */
		public function __get( $key ){

			if( method_exists( $this , "field_$key" ) ){
			    return $this->{"field_$key"}();
			}else{
				trigger_error( "$key is not a property"  , E_USER_WARNING );			
			}
			
		}
		
        /* -- Countable Interface ------------------------- */
		public function count(){
    		$this->fetch();	
			return count( $this->data );
		}
		
        /* -- Array Access Interface ---------------------- */
        public function offsetExists($key) {
            return isset($this->data[$key]);
        }
		/* ------------------------------------------------ */
        public function offsetGet( $key ) {

            if( $this->offsetExists( $key ) )
    			return $this->getItem( $this->data[ $key ] );
			else
                throw new OutOfBoundsException( "$key is not a property" );
        
        }
		/* ------------------------------------------------ */
        public function offsetSet($key, $value) {

        }
		/* ------------------------------------------------ */
        public function offsetUnset( $key ) {
        
        }
        
        /* -- Iterator Interface -------------------------- */
		public function rewind() {
    		$this->fetch();
			reset($this->data);
		}
        /* ------------------------------------------------ */
		public function current() {
    		$this->fetch();
			$current = current($this->data);
			if( $current === false )
			   return false;
			else
    			return $this->getItem( $current );
		}
        /* ------------------------------------------------ */
		public function key() {
    		$this->fetch();
			$var = key($this->data);
			return $var;
		}
        /* ------------------------------------------------ */
		public function next() {
    		$this->fetch();
			$var = next($this->data);
			return $var;
		}
        /* ------------------------------------------------ */
		public function valid() {
    		$this->fetch();
			$var = $this->current() !== false;
			return $var;
		}

	}

?>
