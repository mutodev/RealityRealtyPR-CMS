<?php

    class Search_Operator{

        protected $options;

        public function __construct( $options = array() ){
            $this->options = $options;
        }

        public static function factory( $class  , $options = array() ){
        
            switch( $class ){
                case "="   : $class = "Equals"; break;
                case "!="  : $class = "NotEquals"; break;
                case "~"   : $class = "Contains"; break;
                case "!~"  : $class = "NotContains"; break;
                case "<"   : $class = "LessThan"; break;
                case "<="  : $class = "LessThanOrEqual"; break;
                case ">"   : $class = "GreaterThan"; break;
                case ">="  : $class = "GreaterThanOrEqual"; break;
				case "*"   : $class = "IsNull"; break;
            }
             
            $class = ucfirst( $class );
            $class = "Search_Operator_$class";
            $obj   = new $class( $options );
            return $obj;
        }

        /* Returns the operator */
        public function operator( ){
            return '';
        }

        /* Returns a caption for the operator */
        public function description( ){
            return '';
        }

        /* Returns the dql for that value */
        public function compile( $field , $value ){
            return '';
        }

    }
