<?php

    class Helper_Tabs extends Helper{

        public function __construct( $name , $options = array() ){
            parent::__construct( $name , $options );
            
           $Css        = helper('Css');            
           $Javascript = helper('Javascript');
    
           $Css->add("/yammon/public/tabs/tabs.css");
           $Javascript->add( "/yammon/public/mootools/js/mootools.js" );
           $Javascript->add( "/yammon/public/mootools/js/mootools-more.js" );           
           $Javascript->add("/yammon/public/widget/widget.js");
           $Javascript->add("/yammon/public/widget/widget-tabs.js"); 
                      
        }

        public function setupOptions( ){
            $this->addOption('argument', 't'     );
            $this->addOption('remote'  , false   );            
            $this->addOption('tabs'    , array() );
            $this->addOption('default' , null    );            
        }

        public function setOptions( $options ){

            parent::setOptions( $options );
            $tabs = (array)$this->getOption( 'tabs' );
                        
            foreach( $tabs as $id => $tab ){
            
                if( !is_array( $tab ) ){
                    $tab = array(
                       'label' => $tab
                    );
                }
                
                if( !isset( $tab['label'] ) )
                    $tabs[ $id ]['label'] = Inflector::humanize( $id );

                if( empty( $tab['view'] ) )
                    $tabs[ $id ]['view'] = $id;
                    
            }
            $this->setOption( 'tabs' , $tabs );
            
        }

        public function getTab( $id ){
        
            $tabs = $this->getOption('tabs');
            if( !isset( $tabs[ $id ] ) )
                return null;
                        
            $view    = $tabs[$id]['view'];
            $View    = Action::getView();
            $content = $View->partial( $view , $View->toArray() );
            return $content;            

        }
                
        public function html(){

            $tabs     = (array) $this->getOption('tabs');
            $default  = $this->getOption('default');
            $argument = $this->getOption('argument');
            $remote   = $this->getOption('remote') ? '1' : '0';
                                                    
            //Set Default
            if( !$default )
                $default = array_shift( array_keys( $tabs ) );
                
            if( !array_key_exists( $default , $tabs ) )
                $default = null;
            
            //Get the active
            $active = get( $argument , $default );
            if( !array_key_exists( $active , $tabs ) )
                $active = $default;
            
            $html = new Html();
            $html->open('div' , array( 'id' => $this->getName() , 'class' => 'yammon-tabs' , 'widget' => 'Tabs' , 'widget-tabs-remote' => $remote ) );

                $html->open('div' , array( 'class' => 'yammon-tabs-ul') );
                    $html->open('ul');                
                    foreach( $tabs as $id => $tab ){
                    
                        $label     = t( $tab['label'] );
                        $classes   = array();
                        if( $id == $active ) 
                            $classes[] = 'yammon-tabs-active';
                            
                        $href = url(".".qs( $argument , $id ) );                            
                            
                        $html->open('li' , array( 'class' => $classes ) );
                            $html->open('a' , array("href" => $href , 'rel' => $id ) );
                                $html->open('span');
                                    $html->text( $label );
                                $html->close('span');
                            $html->close('a');
                        $html->close('li');
                        
                    }
                    $html->close('ul');
                $html->close('div');   
                
                $html->open('div' , array( 'class' => 'yammon-tabs-container') );                    
                
                    $html->open('div' , array( 'class' => 'yammon-tabs-container-separator') );
                    $html->close('div');
                    
                    foreach( $tabs as $id => $tab ){
                                                
                        $classes   = array();
                        $classes[] = 'yammon-tabs-content';
                        if( $id == $active ) 
                            $classes[] = 'yammon-tabs-content-active';
                                                        
                        $html->open('div' , array( 'class' => $classes ) );                        

                            $part = Inflector::dotify( get_class( $this )."_".$this->getName() ."_".$id );
                                                       
                            Action::startPart( $part );                                
                            if( (Action::isPartRequest() && Action::isPartRequested( $part ) ) || $id == $active || !$remote )
                                echo $this->getTab( $id );
                            $content = Action::endPart( );
                            $html->text( $content );

                        $html->close('div');                    
                    }
                $html->close('div');
            
            $html->close('div');
            
            return (string)$html;
        
        }
        
        public function getTranslationStrings(){

            $strings = array();
            $tabs    = (array) $this->getOption('tabs');
            
            foreach( $tabs as $name => $tab ){
                if( isset( $tab['label'] ) )
                    $strings[] = $tab['label'];
            }
                        
            return $strings;
            
        }
        
        
    }
