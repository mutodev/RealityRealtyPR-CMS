<?php

    class Helper_Css extends Helper{

        private $elements = array();

        public function inline( $content ){
            $this->addInline($content);
        }

        function addInline( $content, $addTag = true ){
            $key = md5($content);
            $this->elements[ $key ] = array( 'type' => 'inline' , 'content' => $content, 'addTag' => $addTag );
        }


        public function add( $href , $media = null ){
            $key = md5( implode('^', array( $href , $media )) );
            $this->elements[ $key ] = array( 'type' => 'src' , 'content' => $href, 'media' => $media );
        }

        public function remove( $href , $media = null ){
            $key = md5( implode('^', array( $href , $media )) );

            unset( $this->elements[ $key ] );
        }

        public function render( $href = null , $media = null ){

            $Html = helper("html");

            if( !empty( $href ) )
                $elements = array( array('type' => 'src' , 'content' => $href, 'media' => $media ) );
            else
                $elements = $this->elements;

            $html = array();
            $html[] = "\n";
            foreach( $elements as $element ){

                $type    = $element['type'];
                $content = $element['content'];

                $attributes          = array();
                $attributes["type"]  = "text/css";
                $attributes["rel"]   = "stylesheet";

                if( $type == 'src' ){

                    $media = $element['media'];

                    if (!empty($media)) {
                        $attributes["media"] = $media;
                    }
                    $attributes["href"] = $content;

                    $html[] = $Html->tag( "link" , $attributes );
                }
                else {

                    if ( !$element['addTag'] )
                        $html[] = $content;

                    else {
                        $html[] = $Html->start( "link" , $attributes );
                        $html[] = $content;
                        $html[] = $Html->end( "link" );
                    }
                }

            }
            $html[] = "\n";

            return implode( "\n" , $html );

        }

    }
