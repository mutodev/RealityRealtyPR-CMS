<?php

    class ListMax_Core_Filter_Telephone extends ListMax_Core_Filter{
        public static function apply( $value ){
        
            if( empty( $value) ) return "";
    
            $pattern = "^[[:space:]]*\(?([0-9][0-9][0-9])?\)?[[:space:]]*[-.+]?[[:space:]]*([0-9][0-9][0-9])[[:space:]]*[-.+]?[[:space:]]*([0-9][0-9][0-9][0-9])[[:space:]]*$";
            $regs    = array();
            $return  = ereg( $pattern , $value , $regs );
    
            if( empty( $regs ) )
                return $value;
            elseif( count( $regs) == 4 ){
                return "(".$regs[1] . ") " . $regs[2] . "-" . $regs[3];
            }else{
                return $regs[2] . "-" . $regs[2];
            }
    
            return $value;
                
        }
	}
