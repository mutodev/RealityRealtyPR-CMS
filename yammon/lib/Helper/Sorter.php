<?php

    class Helper_Sorter extends Helper{

        const SORTED_ASC    = 'ASC';
        const SORTED_DESC   = 'DESC';
        const NOT_SORTED    = 0;

		private $sorts       = array();
		private $presorts    = array();		

        public function setupOptions(){
        
            $this->addOptions( array(
                "default"   => "date" ,
                "parameter" => "sort" ,
                "separator" => "-"    ,
            ));
            
        }

        public function add( $name , $fields = array() , $description = "" , $dir = null ){

            //Normalize the fields
            $fields = (array) $fields;            
            foreach( $fields as $k => $v){
                if( trim($v) == "" )
                    unset( $fields[ $k ] );
            }
            
            //Create the description
            if( empty( $description ) )
                $description = Inflector::humanize( $name );
            
            //Validate the direction
            if( $dir === true )
                $dir = self::SORTED_ASC;
            elseif( $dir === false )
                $dir = self::SORTED_DESC;            
            elseif( $dir !== null )
                if( !in_array( $dir , array( self::SORTED_ASC , self::SORTED_DESC ) ) )
                    $dir = self::SORTED_ASC;
            
            if( $fields ){
                $this->sorts[ $name ] = array( 
                    "fields"      => $fields , 
                    "description" => $description , 
                    "direction"   => $dir 
                );
            }
            
        }

        public function addPresort( $name ){
            if( !$this->isSortKey( $key ) ){
                return false;
            }            
            $this->presorts[] = $name;
        }

        public function clear(){
            $this->sorts    = array();
            $this->presorts = array();
        }

        public function getDefault(){
        
            $default = $this->getOption('default' );
            if( $this->isSortKey( $default ) )
                return $default;
            else
                return null;
                
        }

        public function getSort( ){
        
            $default          = $this->getDefault();
            $parameter        = $this->getSortParameter();
            $separator        = $this->getSeparator();
            $parameter_value  = get( $parameter );
            $parameter_value  = explode( $separator , $parameter_value );
            $parameter_key    = array_shift( $parameter_value );
            $parameter_dir    = array_shift( $parameter_value );
                   
            //Set the default key                   
            if( !$this->isSortKey( $parameter_key ) ){
                $parameter_key = $default;
            }

            //Set the default dir
            if( !in_array( $dir , array( self::SORTED_ASC , self::SORTED_DESC ) ) ){
                $parameter_dir = self::SORTED_ASC;
            }

            //Return sort
            if( $parameter_key && $parameter_dir ){
                return array( $parameter_key , $parameter_dir );
            }else{
                return null;
            }
        
        }

        public function getSortUrl( $key ){

            if( !$this->isSortKey( $key ) )
                return null;

            $sort_dir = $this->isSorted( $key );

            //Get the new sort direction
            $default_sort_dir = $this->sorts[ $key ]['direction'];
            if( $default_sort_dir !== null ){
                $new_sort_dir = $default_sort_dir;
            }else{
                if( $sort_dir == self::NOT_SORTED )
                    $new_sort_dir = self::SORTED_ASC;
                elseif( $sort_dir == self::SOTED_ASC )
                    $new_sort_dir = self::SORTED_DESC;                
                else
                    $new_sort_dir = self::SORTED_ASC;
            }

            //Create the url
            $sort_parameter = $this->getSortParameter();
            $sort_separator = $this->getSeparator();

            //Get the sort value
            $sort_value = $key;
            if( $new_sort_dir == self::SORTED_DESC ){
                $sort_value .= $sort_separator.self::SORTED_DESC;
            }
            
            return url( ".".qs( $sort_parameter , $sort_value ) );
            
        }

        public function getSortParameter(){
            return $this->getOption('parameter');
        }
        
        public function getSeparator(){
            return $this->getOption('separator');
        }

        public function isSortKey( $key ){
            if( $key == null )
                return false;
            else                
                return isset( $this->sorts[ $key ] );
        }

        public function isSorted( $key ){

            if( !$this->isSortKey( $key ) )
                return self::NOT_SORTED;
                
            @list( $sort_key , $sort_dir ) = $this->getSort();
                                
            if( $sort_key == $key )
                return $sort_dir;
            else
                return self::NOT_SORTED;

        }
        
        public function render( ){

			$html = helper('Html');
       
       		$html->open("strong");
	       		$html->text( t("Order By: ") );
       		$html->close("strong");
       		
       		$html->open("select" , array("onchange" => "window.location.href = this.value" ) );
       		foreach( $this->sorts as $k => $v ){
       			       			
       			$atts             = array();
       			$atts["value"]    = $this->getSortUrl( $k );
       			
       			if( $this->isSorted( $k ) !== self::NOT_SORTED ) 
       				$atts["selected"] = "selected";
	       		
	       		$html->open("option" , $atts );
	       			$html->text( $v['description'] );
	       		$html->close("option" );
	       		
       		}
       		$html->close("select");       		
       		
			return $html->render();
        }        
        
        public function setDefault( $default = null ){
            return $this->setOption('default' , $default );
        }
        
        public function setOptions( $options ){        
            parent::setOptions( $options );
        }
        
        public function setSortParameter( $parameter ){
            $this->setOption( 'parameter' , $parameter );
        }
        
        public function setSeparator( $separator ){
            $this->setOption( 'separator' , $separator );
        }
                
        public function sort( $source ){
        
            $expression = array();
        
            @list( $sort_key , $sort_dir ) = $this->getSort();
                        
            //Add the presorts
            foreach( $this->presorts as $presort ){
                $ex = $this->sortExpression( $presort , self::SORTED_ASC );
                if( $ex ) $expression[] = $ex;
            }
            
            //Make sure the source key exists
            $ex = $this->sortExpression( $sort_key , $sort_dir );
            if( $ex ) $expression[] = $ex;            
           
            //Sort the query
            if( $expression ){
                $expression = implode( " , " , $expression );            
                $source->orderBy( $expression );
            }                    
            
            return $source;
            
        }
        
        protected function sortExpression( $key , $dir ){
                
            //Validate the key
            if( !$this->isSortKey( $key ) ){
                return null;
            }        
            
            //Validate the direction
            if( !in_array( $dir , array(self::SORTED_ASC , self::SORTED_DESC) ) ){
                $dir = self::SORTED_ASC;
            }        

            //Get the fields
            $fields   = $this->sorts[ $key ]['fields'];
            $sort_dir = $this->sorts[ $key ]['direction'];
        
            //Force Direction if needed
            if( $sort_dir !== null )
                $dir = $sort_dir;
        
            //Create expression
            $expression = array();
            foreach( $fields as $field ){
                if( $dir == self::SORTED_DESC )
                    $expression[] = "$field DESC";
                else                    
                    $expression[] = "$field";                
            }
        
            //Return
            if( $expression )
                return implode( " , " , $expression );
            else
                return null;
        
        }
        
    
    }
