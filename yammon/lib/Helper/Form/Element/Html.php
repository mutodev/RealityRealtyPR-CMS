<?php

class Helper_Form_Element_Html extends Helper_Form_Element_TextArea{

  public function setupOptions(){
       parent::setupOptions();
       $this->addOption("theme");
       $this->addOption("config" , array(
          "theme"                             => "advanced" ,
          "height"                            => "350px" ,
          "theme_advanced_toolbar_location"   => "top" ,
          "theme_advanced_toolbar_align"      => "left" ,
          "theme_advanced_statusbar_location" => "bottom" ,
          "theme_advanced_resizing"           => true,
          "relative_urls"                     => false
       ));
       $this->setOption( "size" , "full" );
       $this->setOption( "sanitize" , "html" );
  }

  public function construct(){
       parent::construct();
       $Javascript = helper('Javascript');
       $Javascript->add('/yammon/public/form/js/tinymce/tiny_mce.js');
       $Javascript->add( "/yammon/public/widget/widget.js" );
       $Javascript->add( "/yammon/public/widget/widget-wysiwyg.js" );
  }

  public function render( ){

    $domid    = $this->getDomId( );
    $config   = $this->getOption("config");

    //Basic Configuration
    $config['mode']     = 'exact';
    $config['elements'] = $domid;

    //Render
    $this->addAttribute( 'widget'         , 'Wysiwyg' );
    $this->addAttribute( 'widget-wysiwyg' , json_encode( $config ) );

    return parent::render();
  }
}

