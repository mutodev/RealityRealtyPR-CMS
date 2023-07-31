<?php

    class Helper_Html extends Helper{
    	
    	private $stack   = array();
    	private $content = array();
    	    			
		function clear(){
		    $this->stack   = array();
		    $this->content = array();
		}
		
		function open( $tag , $attributes = array() , $content = '' , $self_close = false ){

/*		   
            HAML Pattern
	        $pattern = "
	        /^
	          (?<tag>  [A-Z0-9-_]+      )?
	          (?<cls>  (\.[A-Z0-9-_]+)+ )?
	          (?<id>   #[A-Z0-9-_]+     )?
	          (?<value>
	            \s*
	            (?<equals>   = )
	            \s*
	            (?<content> .+ )
	            \s*
	          )?
	        $/xi";
*/
	    		    
		    if( $content == '' && ! $self_close  ){		    
    		    $this->stack[]   = $tag;
                $this->content[] = str_repeat( "\t" , count( $this->stack ) );
	    	    $this->content[] = $this->startTag( $tag , $attributes );
	    	    $this->content[] = "\n";
            }else{
                $this->content[] = str_repeat( "\t" , count( $this->stack ) );
	    	    $this->content[] = $this->tag( $tag , $attributes , $content );
	    	    $this->content[] = "\n";            
            }
		}

		function text( $content ){
            $this->content[] = str_repeat( "\t" , count( $this->stack ) + 1 );
    		$this->content[] = $content;
		    $this->content[] = "\n";
		}

		function close( ){
            $tag = array_pop( $this->stack );
            $this->content[] = str_repeat( "\t" , count( $this->stack ) + 1 );
		    $this->content[] = $this->endTag( $tag );
		    $this->content[] = "\n";
		}

		function closeAll( ){
		    while( count( $this->stack ) ){
		        $this->close();
            }		
		}
		
		function render( $options = array() ){
            
		    $this->closeAll();
		    $content = implode( "" , $this->content );
            $this->content = array();
            return $content;
		}
		
		function tag( $tag_name , $attributes = array() , $content = "" ){
		    
		    $html   = array();
		    
		    if( !empty( $content ) ){
    		    $html[] = $this->startTag( $tag_name , $attributes );
	            $html[] = $content;
	            $html[] = $this->endTag( $tag_name );
            }else{
    		    $html[] = $this->startTag( $tag_name , $attributes , true );
            }
		    		    		    		    
            return implode( "" , $html );

		}

        function startTag( $tag_name , $attributes = array() , $self_close = false ){

		    $html = array();
		    
		    if( empty( $attributes ) ){
		        $attributes = array();
		    }
		    
		    //Convert some attributes
		    if( isset( $attributes["class"] ) && is_array( $attributes["class"] ) ){
		        $attributes["class"] = implode( " " , $attributes["class"]  );
		    }

		    if( isset( $attributes["style"] ) && is_array( $attributes["style"] ) ){                
		        $styles = array();
		        foreach( $attributes["style"] as $rule => $value )
		            $styles[] = "$rule : $value";
		        $attributes["style"] = implode( "; " , $styles );
		    }
		    		    
		    $html[] = "<";
		    $html[] = $tag_name;		    
		    $html[] = "";
		    		 
            $attribute_pairs = array();
            foreach( $attributes as $attribute => $value ){
                if( $value !== null )
					@$attribute_pairs[] = $attribute.'="'.htmlspecialchars( $value , ENT_COMPAT ).'"';
            }

            if( !empty( $attribute_pairs ) ){
    		    $html[] = " ".implode( " " , $attribute_pairs );            
            }
    
            if( $self_close )
    		    $html[] = " />";
            else
    		    $html[] = ">";

            return implode( "" , $html );
            
        }
    
        function endTag( $tag_name ){
            return "</{$tag_name}>";
        }
    
    }

