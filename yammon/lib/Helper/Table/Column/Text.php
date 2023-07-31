<?php

    class Helper_Table_Column_Text extends Helper_Table_Column{
    
       /**
        * Render the header for this column
        */
        public function header( ){
            return $this->getLabel();
        }

       /**
        * Render the content for this column
        */
        public function text( $record ){        
            return $this->getValue( $record );
        }    
     
    }
