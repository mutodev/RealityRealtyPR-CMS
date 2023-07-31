<?php

    class Helper_Table_Column_Expander extends Helper_Table_Column{

        public function isGroupable(){
            return false;
        }

        public function isHideable(){
            return false;
        }

       /**
        * Render the header for this column
        */
        public function header( ){
            $output   = array();
            $output[] = "<div class='yammon-table-expander-all yammon-table-expander-all-collapsed'>";
                $output[] = $this->getLabel();
            $output[] = "</div>";
            return implode( "\n" ,$output );
        }

        public function text( $record ){
            $output   = array();
            $output[] = "<div class='yammon-table-expander yammon-table-expander-collapsed'>";
                $output[] = $this->getValue( $record );
            $output[] = "</div>";
            return implode( "\n" ,$output );
        }

        public function extra( $record ){
            $extra = $this->getOption('extra' , '' );
            $extra = t( $extra );
            $output[] = "<div class='yammon-table-expander-content'>";
                $output[] = $this->getValue( $record , $extra );
            $output[] = "</div>";
            return implode( "\n" ,$output );
        }

        public function getTranslationStrings(){

            $strings   = parent::getTranslationStrings();

            $string    = $this->getOption('extra' , '' );
            if( trim( $string ) ) $strings[] = $string;

            return $strings;

        }

    }
