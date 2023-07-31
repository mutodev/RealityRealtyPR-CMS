<?php

    class Helper_Table_Column_Checkbox extends Helper_Table_Column{
    
        private $on_click         = null;
        private $on_header_click  = 'onCheckboxAllClick(this)';
        private $checked          = array();
    
        function __construct( $parent , $name , $options = array() ){        
            parent::__construct( $parent , $name , $options  );
                             
            $this->setOptions( array(
                "sortable"  => false ,
                "groupable" => false ,
                "hideable"  => false
            ));
            
        }
    
        public function setOptions( $options ){

            parent::setOptions( $options );

            if( isset( $options[ 'onClick' ] ) )
                $this->setOnClick( $options[ 'onClick' ] );

            if( isset( $options[ 'onHeaderClick' ] ) )
                $this->setOnHeaderClick( $options[ 'onHeaderClick' ] );

        }
        
        public function setOnClick( $js ){
            $this->on_click = $js;
        }

        public function getOnClick(){
            return $this->on_click;
        }

        public function setOnHeaderClick( $js ){
            $this->on_header_click = $js;
        }

        public function getOnHeaderClick(){
            return $this->on_header_click;
        }
        
        public function getChecked(){
            return $this->checked;
        }
        
        public function setChecked( $checked ){
            $this->checked = $checked;
        }
        
        public function header( ){       
            $name    = $this->getName();    
            $onclick = $this->getOnHeaderClick();

            $html[] = "<input ";
            $html[] = "type='checkbox'" ;
            $html[] = "value='1'";
            $html[] = "column='".$name."'";
            if( $onclick ) 
                $html[] = "onclick='$onclick'" ;
            $html[] = "/>" ;
            return implode( " " , $html );
        }

        public function text( $record ){
            $name    = $this->getName();        
            $value   = $this->getValue( $record );        
            $onclick = $this->getOnClick();
     
            $html   = array();
            $html[] = "<input ";
            $html[] = "name='".$name."[]'" ;
            $html[] = "type='checkbox'" ;
            $html[] = "column='".$name."'";
            $html[] = "value='$value'";

            if( $onclick ) 
                $html[] = "onclick='$onclick'" ;

            if( in_array( $value , $this->checked ) ) 
                $html[] = "checked='checked'" ;
                
            $html[] = "/>" ;
            return implode( " " , $html );
            
        }
     
        function getValues(){
            $name  = $this->getParent()->getName();                
            return post( $name );
        }
     
    }
