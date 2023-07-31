<?php

    class Html{

    	private $stack       = array();
    	private $content     = array();
    	private $selfclosing = array(
    	    "area" ,
    	    "base" ,
    	    "basefont" ,
    	    "br" ,
    	    "hr" ,
    	    "input" ,
    	    "img" ,
    	    "link" ,
    	    "meta" ,
    	);

		public function clear(){
		    $this->stack   = array();
		    $this->content = array();
		}

		public function close( $validate_tag = null ){

            $tag = array_pop( $this->stack );

		    if( $validate_tag ){
    		    $validate_tag = trim( strtolower( $validate_tag ) );

    		    if( $validate_tag !== $tag )
    		        throw new Exception('Html Expecting '.$tag);

            }

            $this->content[] = str_repeat( "\t" , count( $this->stack ) + 1 );
		    $this->content[] = '</'.$tag.'>';
		    $this->content[] = "\n";

		}

		public function closeAll( ){
		    while( count( $this->stack ) ){
		        $this->close();
            }
		}

        public function comment( $content , $mutiline = null ){

            $multiline = $mutiline != null ? $mutiline : (strpos( $content , "\n" ) !== false );
            $this->content[] = str_repeat( "\t" , count( $this->stack ) + 1 );
    		$this->content[] = '<!-- ';
    		$this->content[] = $multiline ? "\n" : '';
    		$this->content[] = $content;
    		$this->content[] = $multiline ? "\n" : '';
    		$this->content[] = ' -->';
		    $this->content[] = "\n";
        }


		public function get(){
		    $this->closeAll();
            return implode( "" , $this->content );
		}

		public function open( $tag , $attributes = array() , $content = null , $self_close = null ){

            //Prepare tag
            $tag = trim( strtolower( $tag ) );

            //Set self closing
            if( $content === null && $self_close === null && in_array( $tag , $this->selfclosing ) ){
                $self_close = true;
            }

            //Add to stack
            if( !$self_close ){
                $this->stack[] = $tag;
            }

            //Create Start Tag
            $this->content[] = str_repeat( "\t" , count( $this->stack ) );
            $this->content[] = $this->start( $tag , $attributes , $self_close );

            //Write Content
            if( $content )
                $this->text( $content );

            //Close tag
            if( $content !== null && $self_close ){
                $this->close();
            }

		}



		public function text( $content ){
            $this->content[] = str_repeat( "\t" , count( $this->stack ) + 1 );
    		$this->content[] = $content;
		    $this->content[] = "\n";
		}

   		public function __toString(){
		    return $this->get();
		}

        public static function start( $tag , $attributes = array() , $self_close = false ){

            //Initialize Output
		    $output = array();

            //Initialize Attributes
		    if( empty( $attributes ) ){
		        $attributes = array();
		    }

		    //Normalize Attributes
		    $attributes_normalized = array();
		    foreach( $attributes as $k => $v ){
		        $k = trim( strtolower( $k ) );
		        if( $k == '' || $k === null  || $v === null )
		            continue;

    		    //Convert Class Attribute From Array
                if( $k == 'class' && is_array( $v ) ){
                    $v = implode( " " , $v );
                }

    		    //Convert Style Attribute From Array
                if( $k == 'style' && is_array( $v ) ){
    		        foreach( $v as $rule => $value )
	    	            $v[ $rule ] = $rule.':'.$value;
                    $v = implode( ";" , $v );
                }

                $attributes_normalized[ $k ] = htmlspecialchars( $v ,ENT_COMPAT , 'UTF-8' );

		    }
		    $attributes = $attributes_normalized;


            //Start the tag
		    $output[] = '<';
		    $output[] = $tag;

            //Add Attributes
            if( $attributes ){
                foreach( $attributes as $k => $v ){
                    $output[] = " ".$k.'="'.$v.'"';
                }
            }

            //Self close tag
            if( $self_close )
    		    $output[] = ' /';

            //Close Tag
            $output[] = '>';

            //Return
            return implode( '' , $output );

        }

        public static function end( $tag )
        {
            return '</'.$tag.'>';
        }

    }

