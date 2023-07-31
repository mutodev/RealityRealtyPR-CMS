<?php

    class Helper_Javascript extends Helper{

        private $elements = array();

        public function add( $href ){
			$key = md5($href);
			$this->elements[ $key ] = array( 'type' => 'src' , 'content' => $href );
        }

        public function addInline( $text, $addTag = true ){
			$key = md5($text );
			$this->elements[ $key ] = array( 'type' => 'inline' , 'content' => $text, 'addTag' => $addTag );
        }

        public function remove( $content ){
            $key = md5( $content );
            unset( $this->elements[ $key ] );
        }

        public function render( $href = null ){

			$Html = new Html();

            if( !empty( $href ) )
                $elements = array( array('type' => 'src', 'content' => $href) );
            else
                $elements = $this->elements;

			$html = array();
            foreach( $elements as $element ){

				$type    = $element['type'];
				$content = $element['content'];
                $attributes          = array();
                $attributes["type"]  = "text/javascript";

				if( $type == 'src' ){
	                $attributes["src"]  = $content;
	                $html[] = $Html->start( "script" , $attributes , "" , false).$Html->end("script");
				}else{

					if ( !$element['addTag'] )
						$html[] = $content;

					else {
		                $html[] = $Html->start( "script" , $attributes );
						$html[] = "//<![CDATA[";
						$html[] = $content;
						$html[] = "//]]>";
						$html[] = $Html->end( "script" );
					}
				}

            }
            $html[] = "\n";

            return implode( "\n" , $html );

        }

    }
