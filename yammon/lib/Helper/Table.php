<?php

    class Helper_Table extends Helper{

        private   $source     = null;
        private   $data       = array();
        private   $columns    = array();

        public function __construct( $name , $options = array() ){
           parent::__construct( $name , $options );

           $this->Html       = helper("Html");
           $Css              = helper("Css");
           $Javascript       = helper("Javascript");

           $Css->add( "/yammon/public/table/css/table.css" );
           $Css->add( "/yammon/public/table/css/table-menu.css" );
           $Css->add( "/yammon/public/pagination/css/pagination.css" );

           $Javascript->add( "/yammon/public/mootools/js/mootools.js" );
           $Javascript->add( "/yammon/public/mootools/js/mootools-more.js" );
           $Javascript->add( "/yammon/public/table/js/table.js" );
           $Javascript->add( "/yammon/public/table/js/table-menu.js" );

           $this->addState('sort'  , null  );
           $this->addState('dir'   , null  );
           $this->addState('group' , null  );
           $this->addState('page'  , null  );
           $this->addState('size'  , null  );
        }

        public function setupOptions(){
           parent::setupOptions();
           $this->addOptions( array(
                "source"             => ""     ,

                "allowSorting"       => true   ,
                "allowGrouping"      => true   ,
                "allowPivot"         => true   ,
                "allowHiding"        => true   ,

                "pagination"         => true   ,
                "pager"              => true   ,
                "summary"            => true   ,

                "menu"               => true   ,

                "defaultSort"        => ""     ,
                "defaultSortDir"     => "ASC"  ,
                "defaultGroup"       => ""     ,
                "defaultPage"        => 0      ,
                "defaultPageSize"    => null   ,

                "searching"          => true   ,
                "empty"              => null   ,
                "empty_search"       => null   ,

                "headers"            => true   ,
                "crop"               => false  ,
           ));

        }

        public function setOptions( $options ){

            $options = parent::setOptions( $options );

            //Get the columns
            $columns = @$options['columns'];

            //Set the default values on the columns
            if( is_array( $columns ) ){
                foreach( $columns as $column_name => $column_definition ){
                    $this->addColumn( $column_name , $column_definition );
                }
            }

        }

       public function addColumn( $column_name , $column_definition ){

            $this->columns[ $column_name ] = Helper_Table_Column::create( $this , $column_name , $column_definition );

            $i     = 0;
            $count = count( $this->columns );
            foreach( $this->columns as $column ){
                $column->setPosition( $i , $count );
                $i++;
            }

       }

       public function removeColumn( $column_name ){

            unset( $this->columns[ $column_name ] );

            $i     = 0;
            $count = count( $this->columns );
            foreach( $this->columns as $column ){
                $column->setPosition( $i , $count );
                $i++;
            }

       }

       protected function getGroupSize( $column , $index ){

            $data         = $this->getData();
            $record_count = count( $data );
            $size         = 0;


            $start_value  = $this->columns[ $column ]->getGroupValue( $data[ $index ] );
            while( $index < $record_count ){

                $current_value = $this->columns[ $column ]->getGroupValue( $data[ $index ] );
                if( $start_value != $current_value )
                    return $size;

                $index++;
                $size++;
            }

            return $size;

        }

       public function getRowCount(){
            $data = $this->getData();
            return count( $data );
       }

       public function getHeaders(){
            return $this->columns;
       }

       public function getColumns(){
            return $this->columns;
       }

       public function getSource( $search = false , $paginate = false , $parent = null ){

            $headers = $this->getHeaders();
            $source  = $this->getOption("source");

            //Create Query
            if( $source instanceof Doctrine_Query ){
                $query = clone $source;
            }else{
                $query = new Doctrine_Query();
                $query->parseDqlQuery( $source );
            }

            //Sort the query
            $orberBy = array();

            //Sort by the group first
            $group_column = $this->getGroupColumn();
            if( $group_column ){
               $orderBy[] = $this->columns[ $group_column ]->getGroupSortExpression();
            }

            //Sort by argument
            $sort_column    = $this->getSortColumn();
            $sort_direction = $this->getSortDirection( $sort_column );

            if( $sort_column ){
                $expression   = $this->columns[ $sort_column ]->getSortExpression();
                $expression   = is_string($expression) ? explode( "," , $expression ) : $expression;
                foreach( $expression as $k => $v ){
                    $expression[ $k ] = $expression[ $k ] . " ".$sort_direction;
                }
                $expression   = implode( " , " , $expression );
                $orderBy[]    = $expression;
            }

            if( !empty( $orderBy ) ){
                $query->orderBy( implode( " , " , $orderBy ) );
            }

            if( $this->isTree() ){

                //Filter to parent
                $tree      = $this->getTreeHeader();
                $owner     = $query->getExpressionOwner("from");
                $parentObj = Doctrine::getTable( $owner )->find( $parent );
                $level     = $tree->getOption( 'level' , 0 );
                $root      = $tree->getOption( 'root'  , null );

                if( $root !== null ){
                    $query->andWhere( 'root_id = ?' , $root );
                }

                if( !$parent ){
                    $query->andWhere( 'level = ?' , $level );
                }else{
                    $query->andWhere( "lft > ? AND rgt < ? AND level = ?" , array($parentObj->lft , $parentObj->rgt , $parentObj->level + 1)  );
                }

                //Search
                $Search = $this->getSearch();
                $dql    = $Search ? $Search->getDQL() : null;
                if( $search && $dql ){

                    //Get the lft and rgt of matched elements
                    $q = new Doctrine_Query();
                    $q->select( 'lft , rgt' );
                    $q->from( $owner );
                    $q->andWhere( $dql );
                    $Collection = $q->execute();

                    $dql = array();
                    foreach( $Collection as $Item ){
                        $dql[] = "(( lft <= ".$Item->lft." AND rgt >= ".$Item->rgt." ) OR ( lft >= ".$Item->lft." AND rgt <= ".$Item->rgt." ))";
                    }

                    if( empty( $dql ) ){
                        $query->andWhere(0);
                    }else{
                        $dql = "( ".implode( " OR " , $dql )." )";
                        $query->andWhere($dql);
                    }

                 }

                 //Paginate
                 if( $paginate && empty( $parent ) ){
                    $this->getPagination()->paginate( $query );
                 }

            }else{

                //Search
                if( $search ){
                    $Search = $this->getSearch();
                    if( $Search ) $Search->search( $query );
                }

                //Paginate
                if( $paginate ){
                    $this->getPagination()->paginate( $query );
                }

            }

            return $query;
       }

       public function getData( $search = false , $paginate = false , $parent = null ){

            if( !is_null($parent) && isset($this->data[ $parent ]) ){
                return $this->data[ $parent ];
            }

            //Get Query
            $query = $this->getSource($search, $paginate, $parent);

            //Get the data
            $this->data[ $parent ] = $query->execute();
            return $this->data[ $parent ];

        }

        public function getPagination(){

            $pagination = $this->getOption('pagination');

            if( !$pagination )
                return null;

            if( $pagination === true )
                $pagination = $this->getName()."_page";

            $Pagination = helper('Pagination' , $pagination );
            $Pagination->setOptions( array(
                'size' => $this->getOption('defaultPageSize')
            ));

            return $Pagination;
        }

        public function getSearch(){

            $searching = $this->getOption('searching');

            if( !$searching )
                return null;

            if( $searching === true )
                $searching = null;

            $Search = helper('Search' , $searching );
            return $Search;
        }


        private function setPage( $page ){
            $Pagination = $this->getPagination();
            if( $Pagination )
                $Pagination->setPage( $page );
        }

        private function getPage( ){
            if( $Pagination )
                return $Pagination->getPage();
            else
                return 0;
        }

        private function setSize( $size ){
            $Pagination = $this->getPagination();
            if( $Pagination )
                $Pagination->setSize( $size );
        }

        public function isTree(){

            $header = $this->getTreeHeader();
            if( $header )
                return true;
            else
                return false;

        }

        public function getTreeHeader(){

           $headers = $this->getHeaders();
            foreach( $headers as $header ){
                if( get_class( $header ) == "Helper_Table_Column_Tree" ){
                    return $header;
                }
            }
            return false;

        }

       /**
        *
        */
        public function isGrouped(){
            return ($this->getGroupColumn() != false);
        }

       /**
        *
        */
        public function hasSearch(){
            $Search = $this->getSearch();
            if( $Search )
                return $Search->hasSearch();
            else
                return null;
        }

       /**
        *
        */
        public function getClearSearchUrl(){
            $Search = $this->getSearch();
            if( $Search )
                return $Search->getClearSearchUrl();
            else
                return null;
        }

       /**
        *
        */
        public function isGroupable(){
            return $this->getOption('allowGrouping');
        }

       /**
        *
        */
        public function isColumnSortable( $column ){

            if( !isset( $this->columns[ $column ] ) )
                return false;
            else
                return $this->columns[ $column ]->isSortable();

        }

       /**
        *
        */
        public function isColumnGroupable( $column ){

            if( !isset( $this->columns[ $column ] ) )
                return false;
            else
                return $this->columns[ $column ]->isGroupable();
        }

       /**
        *
        */
        public function getGroupColumn(){

            $group         = $this->getState('group');
            if( !$group )
                $group = $this->getOption("defaultGroup");

            if( !$this->getOption('allowGrouping') )
                return false;

            if( empty( $group ) )
                return false;

            if( !isset( $this->columns[ $group ] ) )
                return false;

            if( !$this->columns[ $group ]->isGroupable() )
                return false;

            return $group;

        }

       /**
        *
        */
        public function isSortable(){
            return $this->getOption('allowSorting');
        }

       /**
        *
        */
        public function getSortColumn(){

            $sort = $this->getState('sort');

            if( !$sort )
                $sort = $this->getOption("defaultSort");

            //If we are not allowed sorting return false
            if( !$this->getOption('allowSorting') )
                return false;

            //If there is not column to sort return false
            if( empty( $sort ) || !$this->isColumnSortable( $sort ) ){
                return false;
            }

            //Return the sort
            return $sort;

        }

       /**
        *
        */
        public function getSortDirection( $name ){

            $sort = $this->getSortColumn();
            $dir  = $this->getState('dir');
            if( !$dir )
                $dir = $this->getOption('defaultSortDir');

            if( $sort == $name ){
                if( $dir == "DESC" ){
                    return "DESC";
                }else{
                    return "ASC";
                }
            }else{
                return null;
            }

        }

       /**
        *
        */
        public function getSortURL( $name ){

            $sorted          = $this->getSortColumn() == $name;
            $sortable        = $this->isColumnSortable( $name );
            $sort_dir        = $this->getSortDirection( $name );

            //Get the new sorting direction
            if( !$sorted ){
                $new_sort_dir = null;
            }elseif( $sort_dir == "ASC" ){
                $new_sort_dir = "DESC";
            }else{
                $new_sort_dir = null;
            }

            $modified = array(
              "sort"  => $name ,
              "dir"   => $new_sort_dir ,
            );

            $state = $this->getState( null , $modified , true );
            $qs    = qs($state);

            return url(".".$qs);

        }

       /**
        *
        */
        public function isHideable(){
            return $this->getOption('allowHiding');
        }

        public function isCropped(){
            return $this->getOption('crop');
        }

        public function html(){

            $Html = helper('Html');
            $Html->clear();

            $search   = $this->getOption("searching");
            $paginate = $this->getOption("pagination");

            //Get Data
            $data = $this->getData( $search , $paginate );

            $this->renderStart();
            $this->renderContainer( $data );
            $this->renderEnd();

            return $Html->render();

        }

        public function renderStart(){

               $Html = helper('Html');

               $Html->open("div" , array("class" => "yammon-table" , "id" => $this->getName() ) );

               //Render Pagination Summary
               if( $this->getOption("pagination")  && ($this->getOption("summary") || $this->getOption("pager")) ){
                    $Html->open("table" , array( "class" => "table-top" ) );
                        $Html->open("tr");

                            if( $this->getOption("summary") ){
                                $Html->open("td" , array("class" => "table-top-summary" ) );
                                   $Html->text( $this->getPagination()->renderSummary() );
                                $Html->close("td");
                            }

                            if( $this->getOption("pager") ){
                                $Html->open("td" , array("class" => "table-top-pager" ) );
                                   $Html->text( $this->getPagination()->renderPager() );
                                $Html->close("td");
                            }

                        $Html->close("tr");
                    $Html->close("table" );

              }

        }

        public function renderContainer( $data ){

           $Html = helper('Html');

           //Get Container Classes
           $container_classes   = array();
           $container_classes[] = "yammon-table-container";

           if( $this->isGrouped() )
            $container_classes[] = "yammon-table-grouped";

           if( $this->isSortable() )
            $container_classes[] = "yammon-table-sortable";

           if( $this->isGroupable() )
            $container_classes[] = "yammon-table-groupable";

           if( $this->isHideable() )
            $container_classes[] = "yammon-table-hideable";

           if( $this->isCropped() )
            $container_classes[] = "yammon-table-cropped";

           $Html->open("div" , array( "class" => $container_classes ) );
              $this->renderTable( $data );
           $Html->close("div");

        }

        public function renderTable( $data ){

           $Html    = helper('Html');
           $headers = $this->getHeaders();

           $Html->open("table" , array("class" => "yammon-table table table-dark table-hover table-bordered" ) );
              $this->renderTHEAD( $headers );
              $this->renderTBODY( $headers , $data );
           $Html->close("table");

        }

        public function renderTHEAD( $headers ){

            if( !$this->getOption('headers') )
                return;

            $Html = helper('Html');

            $Html->open("thead" , array("class" => "yammon-table-thead" ));
               $Html->open("tr" , array("class" => "yammon-table-row-headers" ) );
                   foreach( $headers as $header ){
                       $header->headerCell();
                   }
               $Html->close("tr");
            $Html->close("thead");

        }

        public function renderTBODY( $headers , $data ){

           $Html = helper('Html');

           $Html->open("tbody");
             $this->renderEmpty( $headers , $data );
             $this->renderRows( $headers , $data );
           $Html->close("tbody");
        }

        public function renderEmpty( $headers , $data ){

            $Html = helper('Html');

            $empty     = t( $this->getOption( 'empty'        , t('There are no results') ) );
            $clear     = t( $this->getOption( 'empty_search' , t('Clear Search') ) );
            $clearhref = $this->getClearSearchUrl();

            $empty_message  = $empty;
            if( $this->hasSearch() && $clear ){
              $empty_message .= "<a class='yammon-table-empty-clear-search' href='$clearhref'>$clear</a>";
            }

            if( $data->count() || empty( $empty_message ) )
                return;

              $row_classes   = array();
              $row_classes[] = "yammon-table-row";
              $row_classes[] = "yammon-table-row-even";
              $row_classes[] = "yammon-table-row-empty";

              $col_classes   = array();
              $col_classes[] = "yammon-table-column";
              $col_classes[] = "yammon-table-column-even";
              $col_classes[] = "yammon-table-column-first";
              $col_classes[] = "yammon-table-column-last";
              $col_classes[] = "yammon-table-column-empty";

              $div_classes   = array();
              $div_classes[] = "yammon-table-empty-message";

              $Html->open("tr" , array( "class" => $row_classes ) );
                $Html->open("td" , array( "class" => $col_classes , "colspan" => count( $headers ) ) );
                    $Html->open("div" , array( "class" => $div_classes ) );
                      $Html->text( $empty_message );
                    $Html->close("div");
                $Html->close("td");
              $Html->close("tr");


        }


        public function renderRows( $headers , $data ){

            $index        = 0;
            $group_column = $this->getGroupColumn();
            foreach( $data as $row ){
                $this->renderGroupRow( $headers , $row , $index , $group_column );
                $this->renderRow( $headers , $row , $index );
                $this->renderExtraRows( $headers , $row , $index  );
                $index++;
            }

        }

        public function renderGroupRow( $headers , $row , $index , $group_column ){

              $Html = helper('Html');

              static $last_group_value = null;

              if( empty($group_column) )
                 return;

              $row_classes = array();
              $row_classes[] = "yammon-table-row-group";

              $col_classes   = array();
              $col_classes[] = "yammon-table-column-group";

              //Get the new group value
              $new_group_value  = $this->columns[ $group_column ]->getGroupValue( $row );
              $last_group_value = $index == 0 ? null : $last_group_value;

              //Display Group Row
              if( $new_group_value !== $last_group_value ){

                    $group_size = $this->getGroupSize( $group_column , $index - 1 );
                    $Html->open("tr" , array( "class" => $row_classes) );
                      $Html->open("th" , array( "class" => $col_classes , "colspan" => count( $headers ) ) );
                        $Html->text( $new_group_value );
                      $Html->close("th");
                    $Html->close("tr");

                    $last_group_value = $new_group_value;

              }

        }

        public function renderRow( $headers , $row , &$index , $level = 0 , $collapsed = false ){

          $Html = helper('Html');

          //Get Row Classes
          $row_classes   = array();
          $row_classes[] = "yammon-table-row";
          $row_classes[] = ($index % 2) == 0 ? "yammon-table-row-even" : "yammon-table-row-odd";
          $row_classes[] = "yammon-table-row-extra";

          if( $level == 0 ){
             $collapsed = false;
          }

          $Html->open("tr" , array( "class" => $row_classes , "style" => $collapsed ? "display:none" : "" ) );
              foreach( $headers as $header ){
                  $header->bodyCell( $row );
              }
          $Html->close("tr");

          //Display tree recursively
          $tree = $this->getTreeHeader();
          if( !$tree ) return;

          //Get the data
          $data      = $tree->data( $row );
          $collapsed = !$tree->isExpanded();

          if( !empty($data) ){
              foreach( $data as $row2 ){
                  if( !$collapsed ) $index = $index + 1;
                  $this->renderRow( $headers , $row2 , $index , $level + 1 , $collapsed );
              }
          }

        }

        public function renderExtraRows( $headers , $row , $index ){

          $Html = helper('Html');

          //Get Row Classes
          $row_classes   = array();
          $row_classes[] = "yammon-table-row";
          $row_classes[] = ($index % 2) == 0 ? "yammon-table-row-even" : "yammon-table-row-odd";
          $row_classes[] = "yammon-table-row-extra";

          $col_classes   = array();
          $col_classes[] = "yammon-table-column";
          $col_classes[] = "yammon-table-column-even";
          $col_classes[] = "yammon-table-column-first";
          $col_classes[] = "yammon-table-column-last";
          $col_classes[] = "yammon-table-column-extra";

          foreach( $headers as $header ){
              $extra = $header->extra( $row );
              if( $extra !== null ){
                  $Html->open("tr" , array( "class" => $row_classes , 'style' => 'display:none' ) );
                      $Html->open("td" , array( "class" => $col_classes , "colspan" => count( $headers ) ) );
                            $Html->text( $extra );
                      $Html->close("td");
                  $Html->close("tr");
              }
          }

        }

        public function renderEnd(){

           $Html = helper('Html');

           $Pagination = $this->getPagination();
           if( $Pagination )
               $Html->text( $Pagination->render() );

           $Html->close("div");

        }

        public function getTranslationStrings(){

            $strings   = parent::getTranslationStrings();
            foreach( $this->columns as $column ){
                $sub_strings = $column->getTranslationStrings();
                $strings     = array_merge( $strings , $sub_strings );
            }

            $string = $this->getOption('empty');
            if( trim( $string ) ) $strings[] = $string;

            $string = $this->getOption('empty_search');
            if( trim( $string ) ) $strings[] = $string;

            return $strings;

        }


    }
