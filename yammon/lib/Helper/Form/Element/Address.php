<?php

    class Helper_Form_Element_Address extends Helper_Form_Element_Container{
    		    
        protected $address1;
        protected $address2;
        protected $city;
        protected $state;
        protected $postal_code;
        protected $country;
             
        public function setupOptions(){
           parent::setupOptions();
		   $this->addOption( "prefix"    , "" );
           
           $this->setOption('label' , null );
           $this->setOption('box_renderer' , array(
              'type'      => '2Column' ,
              'highlight' => true 
           ));

           $this->setOption('layout_renderer' , array(
              'type'    => 'Grid' ,
              'columns' => 4 ,
              'style'   => 'width:500px;'
           ));

           $this->setOption('default_renderers' , array(
              'box_renderer' => array(
                  'type'    => '1Column' ,
                  'margin'  => true  ,
                  'padding' => false ,
                  'border'  => false
               )                  
           ));
                  
        }
             
        public function build(){
        
           $this->setOption("layout"  , "grid" );
           $this->setOption("columns" , 4 );

           $prefix    = $this->getOption( "prefix" );
           $textboxes = $this->getOption( "textboxes" );
           $required  = $this->isRequired();

           if( !empty( $prefix ) )
               $prefix = $prefix."_";

		   $this->setOption("size"       , "full" );           
		   $this->setOption("layout"      , "grid" );
		   $this->setOption("columns"     , "4"  );
		   
           $this->address1 = $this->add( array(
                "name"     => $prefix . "address1" ,
                "type"     => "text" ,
                "label"    => t("Address") ,
                "colspan"  => 4 ,
                "required" => $required ,
                "style"    => "width:99%"
		   ));

		   $this->address2 = $this->add( array(
                "name"     => $prefix . "address2" ,
                "type"     => "text" ,		   
                "label"   => t("Address (cont) ") ,
                "colspan" => 4 ,
                "style"    => "width:98%"                
		   ));
		   
		   $this->city = $this->add( array(
                "name"     => $prefix . "city" ,
                "type"     => "text" ,		   
                "label" => t("City") ,
                "required" => $required ,
                "style"    => "width:98%" ,
                "colwidth" => "25%" ,
           ));
            
		   $this->state = $this->add(array(
                "name"     => $prefix . "state" ,
                "type"     => "UsState" ,			   
                "label"    => t("State") ,
                "style"    => "width:98%" ,
                "colwidth" => "25%" ,                
           ));

		   $this->postal_code = $this->add(  array(
                "name"     => $prefix . "postal_code" ,
                "type"     => "text" ,			   
                "label"    => t("Postal Code") ,
                "required" => $required ,
                "style"    => "width:98%"  ,
                "colwidth" => "25%" ,                
           ));

		   $this->country = $this->add( array(
                "name"     => $prefix . "country" ,
                "type"     => "country" ,			   
                "label" => t("Country") ,
                "required" => $required ,
                "style"    => "width:98%" ,
                "colwidth" => "25%" ,                
           ));
                      
        }        
        
    }

