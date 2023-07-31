<?php

    class Search_Field{

        private $name;
        private $label          = null;
        private $default        = true;
        private $sources        = array();
        private $type           = null;
        private $operators      = array();
        private $options        = array();
        private $source_boolean = 'OR';
        private $values_boolean = 'OR';

        public function __construct( $name , $options = array() ){

            $this->setName( $name );

            if( !is_array( $options ) ){
                $source  = $options;
                $options = array();
                $options['source'] = $source;
            }

            if( isset( $options[ 'label'] ) )
                $this->setLabel( $options[ 'label'] );

            if( isset( $options[ 'default'] ) )
                $this->setDefault( $options[ 'default'] );

            if( isset( $options[ 'source'] ) )
                $this->addSource( $options[ 'source'] );

            if( isset( $options[ 'type'] ) )
                $this->setType( $options[ 'type'] );

            if( isset( $options[ 'sources_boolean'] ) ){
                $this->setSourcesBoolean( $options[ 'sources_boolean'] );
            }

            if( isset( $options[ 'values_boolean'] ) ){
                $this->setValuesBoolean( $options[ 'values_boolean'] );
            }

            if( isset( $options[ 'operators'] ) )
                $this->addOperator( $options[ 'operators'] );
            else{
                $this->addOperator( $this->getType()->operators() );
            }

            $this->options = $options;

        }

        public function setName( $name ){
            return $this->name = $name;
        }

        public function getName(){
            return $this->name;
        }

        public function setSourcesBoolean( $bool ){
            $bool = trim(strtoupper( $bool ));
            if( in_array( $bool , array( 'AND' , 'OR' )))
                $this->source_boolean = $bool;

            return $this->source_boolean;
        }

        public function getSourcesBoolean( ){
            return $this->source_boolean;
        }

        public function setValuesBoolean( $bool ){
            $bool = trim(strtoupper( $bool ));
            if( in_array( $bool , array( 'AND' , 'OR' )))
                $this->values_boolean = $bool;

            return $this->values_boolean;
        }

        public function getValuesBoolean( ){
            return $this->values_boolean;
        }


        public function setLabel( $label ){
            return $this->label = $label;
        }

        public function getLabel(){

            if( $this->label === null ){
                $label = $this->getName();
                $label = str_replace( "_" , " " , $label );
                $label = ucfirst( $label );
                return $label;
            }else
                return $this->label;
        }

        public function isDefault(){
            return $this->default;
        }

        public function setDefault( $b ){
            return $this->default = (bool)$b;
        }

        public function setType( $type ){
            return $this->type = $type;
        }

        public function getType( ){

            if( !empty( $this->type ) ){
                $type = $this->type;
            }else{

                //Get the source
                $sources = $this->getSources();

                //If there is more than one source
                if( count( $sources ) > 1 )
                    return Search_Type::factory( "String" );

                //Get the actual source
                $source       = current( $sources );

                //Get the doctrine type of the row
                $source_parts = explode( "." , $source  );
                $table_name   = array_shift( $source_parts );
                $column_name  = array_pop( $source_parts );
                $table        = Doctrine::getTable( $table_name );
                foreach( $source_parts as $relation_name ){
                    $relation = $table->getRelation( $relation_name );
                    $table    = $relation->getClass( );
                    $table    = Doctrine::getTable( $table );
                }
                $definition    = $table->getColumnDefinition( $column_name );
                $doctrine_type = $definition['type'];

                //Return accorfing to type
                if( in_array( $doctrine_type  , array( 'integer' , 'float' , 'decimal' ) ) )
                    $type = 'Number';
                elseif( in_array( $doctrine_type  , array( 'boolean' ) ) )
                    $type = 'Boolean';
                elseif( in_array( $doctrine_type  , array( 'date' , 'timestamp' , 'time' ) ) )
                    $type = 'Date';
                else
                    $type = 'String';

            }

            if( !($type instanceof Search_Type) ){
               $type = ucfirst( $type );
               $type = Search_Type::factory( $type );
            }


            return $type;
        }

        public function validValue( $value ){

            $type = $this->getType();
            if( empty( $type ) )
                return false;
            else
                return $type->is( $value );

        }

        public function addSource( $sources ){
            $sources = (array) $sources;
            $this->sources = array_merge( $this->sources , $sources );
            return $this->sources;
        }

        public function getSources(){
            return $this->sources;
        }

        public function addOperator( $operators ){

            $operators = (array) $operators;
            foreach( $operators as $k => $v ){

                if( is_numeric( $k ) ){
                    $k = $v;
                    $v = array();
                }else{
                    $v = (array)$v;
                }

                $this->operators[ $k ] = $v;
            }

            return $this->operators;
        }

        public function getOperators(){

            $operators = $this->operators;

            foreach( $operators as $operator => $options ){
                if( !($operator instanceof Search_Operator) ){
                    $operators[ $operator ] = Search_Operator::factory( $operator , $options );
                }
            }

            return $operators;

        }

        public function getDefaultOperator(){

            $operators = $this->getOperators();
            $first     = array_shift( $operators );
            return $first;

        }

        public function getOptions(){
            return $this->options;
        }

        public function getOption( $key , $default = null ){
            if( isset( $this->options[ $key ] ) ){
                $value =$this->options[ $key ];
                if( is_string( $value ) && trim( $value ) == '' ){
                    return $default;
                }else{
                    return $value;
                }
            }else{
                return $default;
            }
        }

    }
