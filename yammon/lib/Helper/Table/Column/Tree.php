<?php

    class Helper_Table_Column_Tree extends Helper_Table_Column{

        public function isGroupable(){
            return false;
        }
    
        public function isHideable(){
            return false;
        }
    
        public function isExpanded(){
            $expanded = $this->getOption('expanded' , false );
            $searched = $this->getParent()->hasSearch();
            return $expanded || $searched;
        }
    
       /**
        * Render the header for this column
        */
        public function header( ){
        
            return $this->getLabel();
        
            $output   = array();
            $output[] = "<div class='yammon-table-tree-icon yammon-table-tree-icon-closed'>";
                $output[] = $this->getLabel();
            $output[] = "</div>";
            return implode( "\n" ,$output );        
        }

       /**
        * 
        */
        public function text( $record ){
                                
            $node         = $record->getNode();
            $has_children = $node->hasChildren();
            $descendants  = $node->getNumberDescendants();
            $level        = $node->getLevel();
            $start_level  = $this->getOption('level'    , 0 );
            $expanded     = $this->isExpanded();
            $margin       = (($level - $start_level )* 15);

            if( $has_children ){
                if( $expanded ){
                    $classes = "yammon-table-tree-icon yammon-table-tree-icon-open";
                }else{
                    $classes = "yammon-table-tree-icon yammon-table-tree-icon-closed";                
                }
            }else{
                $classes = "yammon-table-tree-icon yammon-table-tree-icon-leaf";
            }

            $output[] = "<div class='$classes' style='margin-left:${margin}px;' yammon-tree-id='$record->id' yammon-tree-descendants='$descendants' yammon-tree-level='$level'>";
                $output[] =  $this->getValue( $record );
            $output[] = "</div>";            
            
            return implode( "" , $output );
        }
        
        public function data( $record ){
            return $this->getParent()->getData( true , false , $record->id );                    
        }
        
             
    }
