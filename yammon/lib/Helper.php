<?php

    abstract class Helper extends Component{

        protected $_states = array();
        protected $_state  = array();
        
        public function __destruct(){        
            $this->saveState();
        }

        public function setupOptions(){      
           parent::setupOptions();
           $this->addOptions( array(
               "statefull" => false  ,             
           ));
                      
        }        

        public static function factory( $subclass , $name = null , $options = array() ){
            return self::_factory( 'Helper' , $subclass , $name , $options );
        }
                
        public static function getInstances( $first = false ){
            return self::_getInstances( 'Helper' , $first );
        }        

        protected function addState( $key , $default = null ){                
            $this->_states[ $key ] = $default;
        }
             
        protected function clearState(){
            $this->_state = array();
        }
             
        protected function getState( $key = null , $modifier = array() , $string = false ){

             $name = strtolower( $this->getName() );
             
             $state = array();
             
             //Get Session state
             $statefull = $this->getOption('statefull');
             if( $statefull ){
                 $skey   = $this->getStateKey();
                 $state  = Session::read( $skey , array() );
             }                 
                          
             //Get Query State
             $query = get( $name , '' );
             if( $query ){
                 $state = array();
                 $query = explode( "|" , $query );
                 for( $i = 0 ; $i < count( $query ) ; $i = $i+2 ){
                    if( !empty( $query[$i] ) && isset( $query[$i+1] ) )
                        $state[ $query[$i] ] = $query[$i+1];
                 }                 
             }
             
                 
             //Merge states                
             $state = array_merge( $state , $this->_state , $modifier );
             
             //Clean State
             foreach( $state as $k => $v ){

                if( !array_key_exists($k , $this->_states ) ){
                    unset( $state[ $k ] );
                    continue;
                }
                
                if( $state[ $k ] === null )
                    $state[ $k] = $this->_states[ $k ];
                    
             }
                            
             //Return State
             if( $string ){

                 //Return String
                 $query = array();
                 foreach( $state as $k => $v ){
                     $query[] = $k;
                     $query[] = $v;                     
                 }
                 $query = implode( "|" , $query );
                 return array( $name => $query );
                
             }elseif( empty( $key ) )
                return $state;
             elseif( isset( $state[$key] ) )                   
                return $state[ $key ];
             else
                return null;
      
        }
        
        protected function getStateKey(){
            $class  = get_class( $this );
            $action = Router::getCurrentAction();            
            $name   = strtolower( $this->getName() );
            $key    = "helper.state.$action.$class.$name";        
            return md5( $key );
        }

        protected function saveState( ){ 
            $statefull = $this->getOption('statefull');        
            if( $statefull && $this->_states ){
                $skey   = $this->getStateKey();
                $state  = $this->getState();
                Session::write( $skey , $state );
            }
        }
        
        protected function setState( $key , $value = null ){ 
            if( array_key_exists( $key , $this->_states ) )
                return $this->_state[ $key ] = $value;
        }        
              
        public function css(){
            return null;
        }
        
        public function html(){
            return null;        
        }
        
        public function js(){
            return null;        
        }
           
        public function render( $options = array() ){
                
            //Set Options
            $this->setOptions( $options );
            
            $content   = '';
            $part      = Inflector::dotify( get_class( $this )."_".$this->getName() );                
            Action::startPart( $part );
            echo $this->css();
            echo $this->html();
            echo $this->js();   
            $content = Action::endPart(  );            
            
            return $content;
            
        }
        
        public function __toString(){
            return $this->render();
        }
        
        public function getTranslationStrings(){
            return array();
        }
           
    }

