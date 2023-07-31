<?php

    class Helper_Form_Element_Button extends Helper_Form_Element_Valued{

        public function setupOptions(){
            parent::setupOptions();
            $this->addOption( "disabled" , null );
            $this->addOption( "image"    , null );
            $this->addOption( "href"     , null );
            $this->addOption( "type"     , 'submit' );
            $this->setOption( "box_renderer" , "NoBox" );
         	$this->setOption( 'class'    , '' );
        }

		public function render( ){

            $name     = $this->getName();
            $domname  = $this->getDomName();
            $domid    = $this->getDomId();
            $image    = $this->getOption("image");
            $href     = $this->getOption("href");
            $disabled =	$this->getOption("disabled");
            $type     = $this->getOption('type');
            $label    = $this->getLabel();

		   	//Create the Label
		   	$content_label = array();
		   	if( $image ) $content_label[] = "<img src='$image' />";
            if( $label ) $content_label[] = $label;
            $content_label = implode( " " , $content_label );

		   	$content   = array();
		   	if( $href ){

                $this->addAttribute( 'href', url($href) );
                if( $disabled )
                    $this->addClass('disabled');

                $this->addClass('button');
                $this->addClass('btn');
                $attributes = $this->getAttributes();

    		   	$content[] = "<a $attributes>";
    		   	$content[] = $content_label;
    		   	$content[] = "</a>";

            }else{

                $this->addAttribute( 'id'   , $domid );
                $this->addAttribute( 'name' , $domname );
                $this->addAttribute( 'href' , url($href) );
                $this->addAttribute( 'type' , 'submit' );
                $this->addAttribute( 'value', $name );

                if( $disabled )
                    $this->addAttribute( 'disabled', 'disabled' );

                $this->addClass('button');
                $this->addClass('btn');
                $attributes = $this->getAttributes();

    		   	$content[] = "<button $attributes>";
    		   	$content[] = $content_label;
    		   	$content[] = "</button>";
            }

		   	return implode( "\n" , $content );

        }

    }
