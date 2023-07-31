<?php

    /**
        TODO: Do Geolocation for default position
              right now it goes to puerto rico
    **/

    class Helper_Form_Element_GoogleMap extends Helper_Form_Element_Valued{

        public function setupOptions(){
            parent::setupOptions();
            $this->addOption( 'width'             , null        );
            $this->addOption( 'height'            , '300px'     );
            $this->addOption( 'zoom'              , 8           );
            $this->addOption( 'lat'               , '18.231960055191518');            
            $this->addOption( 'lng'               , '-66.478271484375');   
            $this->addOption( 'api_key'           , Configure::read("google.map.api") );
        }
        
        public function isPresent( $value ){
            return !empty( $value['lat'] ) && !empty( $value['lng']);
        }
        
        public function build(){
            $api_key    = $this->getOption( "api_key" );        
            $Javascript = helper('Javascript');
            $Javascript->add( 'http://maps.google.com/maps/api/js?sensor=false&v=3&key=' . $api_key );
            $Javascript->add( '/yammon/public/form/js/googlemap.js' );        
        }

        public function render(){

            $domid     = $this->getDomId();
            $domname   = $this->getDomName();
            $value     = $this->getValue();

            $attributes = $this->getAttributes( true );        
            $attributes['id']               = $this->getDomId();
            $attributes['lat']              = $this->getOption('lat');            
            $attributes['lng']              = $this->getOption('lng');                        
            $attributes['zoom']             = $this->getOption('zoom');            
            $attributes['style']['width']   = $this->getOption('width' , '100%' );
            $attributes['style']['height']  = $this->getOption('height');
            $attributes['class'][]          = 'ym-form-map';
            
            $attributes_lat = array();
            $attributes_lat['type']   = 'hidden';
            $attributes_lat['id']     = $domid."_lat";
            $attributes_lat['name']   = $domname."[lat]";
            $attributes_lat['value']  = @$value['lat'];
            
            $attributes_lng = array();
            $attributes_lng['type']  = 'hidden';
            $attributes_lng['id']    = $domid."_lng";
            $attributes_lng['name']  = $domname."[lng]";
            $attributes_lng['value']  = @$value['lng'];            
            
            $attributes_zoom = array();
            $attributes_zoom['type']  = 'hidden';
            $attributes_zoom['id']    = $domid."_zoom";
            $attributes_zoom['name']  = $domname."[zoom]";
            $attributes_zoom['value'] = @$value['zoom'];
            
            $Html = new Html();            
            $Html->open(  'div'   , $attributes );
            $Html->close( 'div');
            $Html->open( 'input' , $attributes_lat  , null , true );            
            $Html->open( 'input' , $attributes_lng  , null , true );
            $Html->open( 'input' , $attributes_zoom , null , true );            
            return $Html->get();

        }


        
    
    }
