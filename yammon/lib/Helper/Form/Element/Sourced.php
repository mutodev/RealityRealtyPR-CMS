<?php

    abstract class Helper_Form_Element_Sourced extends Helper_Form_Element_Valued{

        private $key_field      = null;
        private $label_field    = null;
        private $key_template   = null;
        private $label_template = null;
        private $group_template = null;
        private $values         = array();
        private $flat_values    = array();

        public function setupOptions(){
            parent::setupOptions();
            $this->addOption( "options"              , array() );
            $this->addOption( "empty"                , null );
            $this->addOption( "source"               , ""   );
            $this->addOption( "source_key"           , ""   );
            $this->addOption( "source_label"         , ""   );
            $this->addOption( "source_level"         , ""   );
            $this->addOption( "source_group"         , ""   );
            $this->addOption( "source_level_start"   , 0    );
            $this->addOption( "source_key_tpl"       , ""   );
            $this->addOption( "source_label_tpl"     , ""   );
            $this->addOption( "source_group_tpl"     , ""   );
            $this->addOption( "source_max_results"   , null );
            $this->addOption( "source_search_fields" , array() );
            $this->addOption( "source_validate"      , true );
            $this->addOption( "source_callback"      , null );
            $this->addOption( "source_nested"        , null );
        }

        public function getValue(){

            $validate = $this->getOption("source_validate");
            $values   = parent::getValue();

            if( $validate ) {
                $options = $this->getPossibleValues( null , false , true );
                $isArray = is_array($values);
                $values  = (array) $values;

                foreach( $values as $key => $value ) {
                    if( @!array_key_exists( $value , $options ) )
                        unset($values[$key]);
                }

                if ( empty($values) )
                    $values = null;
                else if ( !$isArray )
                    $values = array_shift($values);
            }

            return $values;
        }

        public function getPossibleValues( $search = null , $limit = true , $flat = false  ){

            $callback = $this->getOption( "source_callback" );
            $options  = (array)$this->getOption( "options" );
            $source   = $this->getSource( $search  , $limit );
            $nested   = $this->getOption( "nested" );
            $empty    = $this->getOption("empty");
            $values   = array();

            //Translate the options
            if( $options ){
                foreach( $options as $k => &$v ){

                    //Translate the options
                    if( is_array( $v ) ){
                        foreach( $v as $k2 => &$v2 ){
                            $v2 = t($v2);
                        }
                    }else{
                        $v = t($v);
                    }

                    //Add them to the values array
                    $values[ $k ] = $v;

                }
            }

            //Check if there is a callback
            if( $callback ){

                $return = call_user_func( $callback , $this );
                if( $return instanceof Doctrine_Query )
                    $source = $return;
                else
                    $values = (array) $return;

            }

            $cache_key = $this->getFullName() . ":" . $this->getOptionsLastChangeTime();

            //If there is no source return options
            if( $source ){

                //Check the cache ( dont cache when nested )
                $params = serialize( $source->getParams() );
                $cache_key = ( (string) $source ) . ":" . $params . ":" . $this->getOptionsLastChangeTime();

                if( $flat && isset( $this->flat_values[ $cache_key ] ) )
                    return $this->flat_values[ $cache_key ];

                if( !$flat && isset( $this->values[ $cache_key ] ) )
                    return $this->values[ $cache_key ];

                //Fetch the data
                $records = $source->execute();

                foreach( $records as $record ){

                    list( $key , $label , $group ) = $this->getPossible( $source , $record  );

                    if( $group !== null ){
                        if( !isset( $values[ $group ] ) )
                            $values[ $group ] = array();
                        $values[ $group ][ $key ] = $label;
                    }else
                        $values[ $key ] = $label;
                }

            }

            //Add the empty value
            if( $empty )
                $values =  array('' => t($empty)) + $values;

            //Flatten Options
            $flat_values = array();
            foreach( $values as $k => $v ){

                if( !is_array( $v ) ){
                    $flat_values[ $k ] = $v;
                    continue;
                }

                foreach( $v as $k2 => $v2 ){
                    $flat_values[ $k2 ] = $v2;
                }

            }

            //Cache options
            $this->flat_values[ $cache_key ] = $flat_values;
            $this->values[$cache_key]        = $values;


            //Return
            if( $flat )
                return $flat_values;
            else
                return $values;


        }

        protected function getPossible( $source , $record ){

            $key_template    = $this->getSourceKeyTemplate( $source );
            $label_template  = $this->getSourceLabelTemplate( $source );
            $group_template  = $this->getSourceGroupTemplate( $source );

            $level_field     = $this->getOption('source_level');
            $level_start     = $this->getOption('source_level_start');

            $level_template  = "%{".$level_field."}";
            $keyTemplate     = new Template( $key_template );
            $labelTemplate   = new Template( $label_template );

            $key     = $keyTemplate->apply( $record );
            $label   = $labelTemplate->apply( $record );

            //Get the Group
            $group = null;
            if( $group_template ){
                $groupTemplate  = new Template( $group_template );
                $group          = $groupTemplate->apply( $record );
            }

            if( $level_field ){
                $levelTemplate = new Template( $level_template );
                $level         = $levelTemplate->apply( $record );
                $label         = str_repeat( "&nbsp;" , ($level-$level_start) *2 )." - ".$label;
            }

            return array( $key , $label , $group );

        }

        protected function getSource( $search = null , $limit = true ){

            //Get the source
            $source = $this->getOption( "source" );

            if( empty( $source ) )
                return null;

            if( is_object( $source ) )
                $source = clone( $source );

            //Check if nested conditions are met
            $nested_fields = $this->getOption( "nested" );
            if( $nested_fields ){

                $Form          = $this->getForm();
                $nested_fields = (array) $nested_fields;
                $nested_values = array();
                $connection    = Doctrine_Manager::connection();

                foreach( $nested_fields as $nested_field ){

                    //Get the nested element
                    $nested_element = $this->getRelative( $nested_field );
                    if( !$nested_element )
                        return null;

                    //Get the nested value
                    $nested_value = $nested_element->getUnfilteredValue( );
                    if( $nested_value === null || $nested_value == '' )
                        $nested_value = null;

                    //Save the value
                    $nested_value = trim( $connection->quote( $nested_value ), '\'\"' );
                    $nested_values[ $nested_field ] = $nested_value;

                }

                if ( empty($nested_values) )
                    return null;

                //Convert query to string ( to do substitution )
                if( $source instanceof Doctrine_Query )
                    $source = (string) $source;

                //Replace values
                $template   = new Template( $source, false );
                $template->setExpand(false);
                $source     = $template->apply( $nested_values );

            }

            //Convert String to query
            if( is_string( $source ) ){

              //If its only a table name add the from
              if( !preg_match( "/\bFROM\b/i" , $source ) ){
                  $source = "FROM $source";
              }

              //Parse Query
              $query = new Doctrine_Query();
              $source = $query->parseDqlQuery( $source );

            }

            //Search
            if( $search  ){

                $search_fields = $this->getOption('source_search_fields');

                //Do Automagic Search
                if( !$search_fields ){

                    $name = $source->getExpressionOwner("from");
                    $Search = new Search();
                    $Search->setOptions( array('source' => $name ) );
                    $Search->search( $query , $search );

                }else{ //Search by specific fields

                    $connection = Doctrine_Manager::connection();
                    $fields     = (array) $search_fields;
                    $keywords   = $keywords = preg_split("/[\s,]+/", $search );
                    $where    = array();
                    foreach( $keywords as $keyword ){
                        $subwhere = array();
                        $keyword  = $connection->quote( '%'.$keyword.'%' );
                        foreach( $fields as $field ){
                            $subwhere[] = "$field LIKE $keyword";
                        }
                        $where[] = "(".implode(' OR ' , $subwhere ).")";
                    }
                    $where = "(".implode( ' AND ' , $where ).")";
                    $query->andWhere( $where );

                }

            }

            //Limit
            $max_results = $this->getOption( "source_max_results" );
            if( $limit && $max_results ){
                $source->limit( $max_results );
            }

            //Return Source
            return $source;

        }


        protected function getSourceKey( $source ){

            if( $this->key_field )
                return $this->key_field;

            //Check if a key is defined
            $key = $this->getOption( "source_key" );
            if( $key ){
                return $this->key_field = $key;
            }

            //Try to guess the key from the source

            //Get Columns
            $name     = ($source instanceof Doctrine_Query) ? $source->getExpressionOwner("from") : $source;
            $table    = Doctrine::getTable( $name );
            $columns  = $table->getColumns();

            //Get the Primary Column
            foreach( $columns as $column_name => $column ){
                if( @$column['primary'] === true ){
                    return $this->key_field = $column_name;
                }
            }


            //There is no key
            return $this->key_field = null;

        }

        protected function getSourceKeyTemplate( $source ){

           if( $this->key_template )
                return $this->key_template;

            $template = $this->getOption('source_key_tpl');
            $sourcef  = $this->getSourceKey( $source );

            if( $template == '' && $sourcef ){
                $template = '%{'.$sourcef."}";
            }

            return $this->key_template = $template;

        }

        protected function getSourceLabel( $source ){

            if( $this->label_field )
                return $this->label_field;

            //Check if the label is defined
            $label = $this->getOption( "source_label" );
            if( $label ){
                return $this->label_field = $label;
            }

            //Try to guess the label from the source

            //Get Columns
            $name     = ($source instanceof Doctrine_Query) ? $source->getExpressionOwner("from") : $source;
            $table    = Doctrine::getTable( $name );
            $columns  = $table->getColumns();

            //Get the first string column
            foreach( $columns as $column_name => $column ){
                if( @$column['type'] === 'string' ){
                    return $this->label_field = $column_name;
                }
             }


            //There is label
            return $this->label_field = null;

        }

        protected function getSourceLabelTemplate( $source ){

           if( $this->label_template )
                return $this->label_template;

            $template = $this->getOption('source_label_tpl');
            $sourcef  = $this->getSourceLabel( $source );

            if( $template == '' && $sourcef ){
                $template = '%{'.$sourcef."}";
            }elseif( $template != '' ){
                $template = t( $template );
            }

            return $this->label_template = $template;

        }

        protected function getSourceGroup( $source ){

            //Check if the label is defined
            $group = $this->getOption( "source_group" );
            if( $group ){
                return $group;
            }

        }

        protected function getSourceGroupTemplate( $source ){

            if( $this->group_template )
                return $this->group_template;

            $template = $this->getOption('source_group_tpl');
            $sourcef  = $this->getSourceGroup( $source );

            if( $template == '' && $sourcef ){
                $template = '%{'.$sourcef."}";
            }elseif( $template != '' ){
                $template = t( $template );
            }

            return $this->group_template = $template;

        }

        public function getTranslationStrings(){

            $strings   = parent::getTranslationStrings();

            $string    = $this->getOption('empty');
            if( $string ) $strings[] = $string;

            $options   = $this->getOption('options');
            if( !is_array( $options ) )
                foreach( $options as $k => $v )
                    if( !is_array( $v ) )
                        $strings[] = $v;
                    else{
                        $strings[] = $k;
                        foreach( $v as $k2 => $v2 )
                            $strings[] = $v2;
                    }

            $label_tpl = $this->getOption('source_label_tpl');
            if( $label_tpl ) $strings[] = $label_tpl;

            $group_tpl = $this->getOption('source_label_tpl');
            if( $group_tpl ) $strings[] = $group_tpl;

            return $strings;

        }

        protected function normalizeValue( $values ){

            //Normalize Array Values
            if ( is_array($values) && isset($values[0]) && is_array($values[0]) ) {
                $source      = $this->getOption( "source" );
                $keyTemplate = $this->getSourceKeyTemplate( $source );

                foreach( $values as &$value )
                    $value = Template::create($keyTemplate)->apply( $value );
            }

            return $values;
        }

        final public function render(){
            $html = array();
            $html[] = $this->renderStart();
            $html[] = $this->renderBody();
            $html[] = $this->renderEnd();
            return implode( "\n" , $html );
        }


        abstract public function renderStart();
        abstract public function renderBody();
        abstract public function renderEnd();

    }
