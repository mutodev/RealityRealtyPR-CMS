<?php

    class Helper_Table_Column_Radio extends Helper_Table_Column_Text{
    
        private $checked = array();
    
        function __construct( $parent , $name , $options = array() ){        
            parent::__construct( $parent , $name , $options  );
                             
            $this->setOptions( array(
                "sortable"  => false ,
                "groupable" => false ,
                "hideable"  => false
            ));
            
        }
        
        public function text( $record ){
            $name    = $this->getName();        
            $value   = $this->getValue( $record );        
     
            $html   = array();
            $html[] = "<input ";
            $html[] = "name='".$name."'";
            $html[] = "type='radio'" ;
            $html[] = "value='$value'";
                
            $html[] = "/>" ;
            return implode( " " , $html );
            
        }
          
    }
