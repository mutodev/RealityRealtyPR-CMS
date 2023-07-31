<?php

    abstract class Helper_Table_Column{

        private $name;
        private $classname             = '';
        private $label                 = null;
        private $index                 = 0;
        private $count                 = 0;
        private $source                = '';
        private $sort_expression       = '';
        private $group_sort_expression = '';
        private $width                 = 'auto';
        private $groupable             = true;
        private $sortable              = true;
        private $hideable              = true;
        private $template              = null;
        private $template_group        = null;
        private $decorator             = null;
        private $options               = array();

       /**
        * Constructor
        */
        public function __construct( $parent , $name , $options = array() ){
            $this->parent = $parent;
            $this->name   = $name;
            $this->setOptions( $options );
        }

        /**
            Create a new Column
        **/
        static function create( $parent , $name , $options = array() ){

            $type  = empty( $options['type'] ) ? 'Text' : $options['type'];
            $type  = ucfirst( $type );
            $class = "Helper_Table_Column_".$type;

            if( !class_exists( $class ) ){
                throw new Helper_Table_ColumnNotFoundException("Cound't find table decorator '$name'");
            }

            return new $class( $parent , $name , $options );

        }


       /**
        * Set Options
        */
        public function setOptions( $options ){

            if( !empty( $options[ 'source' ] ) )
                $this->setSource( $options[ 'source' ] );

            if( !empty( $options[ 'label' ] ) )
                $this->setLabel( $options[ 'label' ] );

            if( !empty( $options[ 'class' ] ) )
                $this->setClass( $options[ 'class' ] );

            if( !empty( $options[ 'width' ] ) )
                $this->setWidth( $options[ 'width' ] );

            if( isset( $options[ 'sortable' ] ) )
                $this->setSortable( $options[ 'sortable' ] );

            if( isset( $options[ 'groupable' ] ) )
                $this->setGroupable( $options[ 'groupable' ] );

            if( isset( $options[ 'hideable' ] ) )
                $this->setHideable( $options[ 'hideable' ] );

            if( !empty( $options[ 'sort' ] ) )
                $this->setSortExpression( $options[ 'sort' ] );

            if( !empty( $options[ 'group' ] ) )
                $this->setGroupSortExpression( $options[ 'group' ] );

            if( isset( $options[ 'template' ] ) )
                $this->setTemplate( $options[ 'template' ] );
            elseif( isset( $options[ 'content' ] ) )
                $this->setTemplate( $options[ 'content' ] );

            if( !empty( $options[ 'template_group' ] ) )
                $this->setGroupTemplate( $options[ 'template_group' ] );

            if( !empty( $options[ 'decorator' ] ) )
                $this->setDecorator(  $options[ 'decorator' ] );

            $this->options = $options;

        }

       /**
        * Get Option
        */
        public function getOption( $key , $default = null ){
            if( isset( $this->options[ $key ] ) ){
                return $this->options[ $key ];
            }else{
                return $default;
            }
        }

       /**
        * Set Option
        */
        public function setOption( $key , $value ){
            $this->options[ $key ] = $value;
        }

       /**
        * Get the name of the column
        */
        public function getName(){
            return $this->name;
        }

       /**
        * Get the table instance that contains this columns
        */
        public function getParent(){
            return $this->parent;
        }

       /**
        *
        */
        public function getLabel(){
            $label = t( $this->label );
            if( $label === null )
                return Inflector::humanize( $this->getName() );
            else
                return $label;
        }

       /**
        *
        */
        public function setLabel( $label ){
            $this->label = $label;
        }

       /**
        * Set the position of this column in respect to the other columns
        */
        public function setPosition( $index , $count ){
            $this->index = $index;
            $this->count = $count;
            $this->even  = ( $index % 2 == 0 );
        }

       /**
        * Set width of the column
        */
        public function setWidth( $width ){
            $this->width = $width;
        }

       /**
        * Get the width of the column
        * TODO: Calculate width
        */
        public function getWidth(  ){
            return $this->width;
        }

       /**
        * Set a css class for the column
        */
        public function setClass( $class ){
            $this->classname = $class;
        }

       /**
        * Get the css class for the column
        */
        public function getClass(){
            return $this->classname;
        }

       /**
        * Get all css classes for this column
        */
        public function getClasses( $header = false ){

            $classes   = array();
            $class     = $this->classname;
            $index     = $this->index;
            $count     = $this->count;

            if( !empty( $class ) )
                $classes[] = $class;

            if( $header ){

                $classes[] = "yammon-table-header";

                if( $this->even )
                    $classes[] = 'yammon-table-header-even';
                else
                    $classes[] = 'yammon-table-header-odd';

                if( $index == 0 )
                    $classes[] = 'yammon-table-header-first';

                if( $index == $count - 1 )
                    $classes[] = 'yammon-table-header-last';

                if( $this->isSorted() ){
                    $classes[] = 'yammon-table-header-sorted';
                }

                if( $this->isSortable() )
                  $classes[] = "yammon-table-header-sortable";

                if( $this->isGroupable() )
                  $classes[] = "yammon-table-header-groupable";

                if( $this->isHideable() )
                  $classes[] = "yammon-table-header-hideable";

            }else{

                $classes[] = "yammon-table-column";

                if( $this->even )
                    $classes[] = 'yammon-table-column-even';
                else
                    $classes[] = 'yammon-table-column-odd';

                if( $index == 0 )
                    $classes[] = 'yammon-table-column-first';

                if( $index == $count - 1 )
                    $classes[] = 'yammon-table-column-last';

                if( $this->isSorted() ){
                    $classes[] = 'yammon-table-column-sorted';
                }

            }

            return implode( " " , $classes );

        }

       /**
        * Set the source of the column
        */
        public function setSource( $source ){
            $this->source = $source;
        }

       /**
        * Get the source of the column
        */
        public function getSource(){
            return $this->source;
        }

       /**
        *
        */
        public function setTemplate( $template ){
            $this->template = $template;
        }

       /**
        *
        */
        public function getTemplate(){
            return t($this->template);
        }

       /**
        *
        */
        public function setGroupTemplate( $template ){
            $this->group_template = $template;
        }

       /**
        *
        */
        public function getGroupTemplate(){
            return t($this->group_template);
        }

       /**
        *
        */
        public function isSorted(  ){
            return $this->parent->getSortColumn() == $this->name;
        }

       /**
        *
        */
        public function isSortable(){
            $expression = $this->getSortExpression();
            return $this->sortable && !empty( $expression );
        }

       /**
        *
        */
        public function setSortable( $sortable ){
            $this->sortable = !!$sortable;
        }

       /**
        *
        */
        public function getSortExpression(){

            if( empty( $this->sort_expression ) )
                return $this->getSource();
            else
                return $this->sort_expression;

        }

       /**
        *
        */
        public function setSortExpression( $expression ){
            $this->sort_expression = $expression;
        }

       /**
        *
        */
        public function getSortURL(){
            return $this->parent->getSortURL( $this->name );
        }

       /**
        *
        */
        public function isGrouped(){
            return $this->parent->getGroupColumn() == $this->name;
        }

       /**
        *
        */
        public function isGroupable(){
            $expression = $this->getGroupSortExpression();
            return $this->groupable && !empty( $expression );
        }

       /**
        *
        */
        public function setGroupable( $groupable ){
            $this->groupable = !!$groupable;
        }

       /**
        *
        */
        public function getGroupSortExpression(){
            if( empty( $this->group_sort_expression ) )
                return $this->getSortExpression();
            else
                return $this->group_sort_expression;
        }

       /**
        *
        */
        public function setGroupSortExpression( $expression ){
            $this->group_sort_expression = $expression;
        }

       /**
        *
        */
        public function getSortDirection(){
            return $this->parent->getSortDirection( $this->name );
        }

       /**
        *
        */
        public function isHideable(){
            return $this->hideable;
        }

       /**
        *
        */
        public function setHideable( $hideable ){
            $this->hideable = !!$hideable;
        }


       /**
        *
        */
        public function getValue( $record , $template = null ){

            $source   = $this->source;

            //Check if we are using a template to get the value
            if( $template === null && $this->template !== null ){
                $template = $this->template;
            }

            //Return the value by the template
            if( $template !== null ){

                $template = new Template( $template );
                $value = $template->apply( $record );

                //Apply Decorator
                $decorator = $this->getDecorator();
                if( $decorator ){
                    $value = $decorator->apply( $value );
                }

                return $value;
            }

            //Remove the prefix
            $source = $this->getSource();
            $prefix = get_class( $record ) . ".";
            if( substr( $source , 0 , strlen( $prefix ) ) == $prefix ){
                $source = substr( $source , strlen( $prefix ) );
            }

            if( empty( $source ) )
                return '';

            //Drill Down to the value
            $value = $record;
            $parts = explode( "." , $source );
            while( $part = array_shift( $parts ) ){
                if( !isset( $value[ $part ] ) )
                    return null;
                $value = $value[ $part ];
            }

            //Apply Decorator
            $decorator = $this->getDecorator();
            if( $decorator ){
                $value = $decorator->apply( $value );
            }

            return $value;

        }

       /**
        *
        */
        public function getGroupValue( $record , $template = null ){

            if( $template === null )
                if( !empty( $this->group_template ) )
                    $template = $this->group_template;

            return $this->getValue( $record , $template );

        }

       /**
        *
        */
        function setDecorator( $decorator ){

            $options = array();

            if ( is_array($decorator) && isset($decorator['name']) ) {

                $options   = $decorator;
                $decorator = $decorator['name'];

                unset($options['name']);
            }

            $this->decorator = Helper_Table_Decorator::create($decorator, $options);
        }

       /**
        *
        */
        function getDecorator( ){
            return $this->decorator;
        }

       /**
        * Header Content
        */
        public function headerCell( ){

          $Html            = helper("Html");
          $name            = $this->getName();
          $type            = substr( get_class( $this ) , strlen("Helper_Table_Column_") );
          $classes         = $this->getClasses( true );
          $label           = $this->getLabel();
          $width           = $this->getWidth();
          $sorted          = $this->isSorted();
          $sortable        = $this->isSortable();
          $sort_dir        = $this->getSortDirection();
          $url             = $this->getSortURL();

          $Html->open("th" , array( "column" => $name ,  "columntype" => $type ,  "class" => $classes , "style" => array( "width" => $width ) ));
              $Html->open("div" , array("class" => "yammon-table-dropdown") );

                  if( $sortable )
                      $Html->open( "a" , array( "href" => $url , 'class' => 'yammon-table-column-link' ) );
                  else
                      $Html->open( "span" );

                      $Html->text( $this->header() );

                      if( $sorted ){
                        if( $sort_dir == "DESC" ){
                          $Html->open( "img" , array("src" => "/yammon/public/table/img/desc.gif" ) , false , true  );
                        }else{
                          $Html->open( "img" , array("src" => "/yammon/public/table/img/asc.gif" )  , false , true  );
                        }
                      }

                  $Html->close();

              $Html->close("div");
          $Html->close("th");


        }

       /**
        * Body Content
        */
        public function bodyCell( $record ){

           static $level = -1;

           $Html    = helper("Html");
           $name    = $this->getName();
           $classes = $this->getClasses();

           $Html->open( "td" , array( "class" => $classes ) );
                $Html->open("div" , array("class" => "yammon-table-column-container") );
                    $Html->text( $this->text( $record ) );
                $Html->close('div');
           $Html->close("td" );

        }

       /**
        * Render the header for this column
        */
        abstract public function header( );

       /**
        *
        */
        abstract public function text( $record );

        public function extra( $record ){
            return null;
        }

        public function data( $record ){
            return null;
        }

        public function getTranslationStrings(){

            $strings   = array();

            $string = $this->getLabel();
            if( trim( $string ) ) $strings[] = $string;

            $string = $this->template;
            if( trim( $string ) ) $strings[] = $string;

            $string = $this->template_group;
            if( trim( $string ) ) $strings[] = $string;

            return $strings;

        }

    }
