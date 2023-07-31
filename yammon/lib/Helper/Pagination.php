<?php

    class Helper_Pagination extends Helper{

		protected $count     = 0;
		protected $url       = '.';
		protected $arguments = null;

        public function __construct( $name , $options = array() ){
            parent::__construct( $name , $options );

           $Css = helper("css");
           $Css->add( "/yammon/public/pagination/css/pagination.css" );
           $this->addState('page' , 0  );
           $this->addState('size' , $this->getOption('size') );

        }

        public function setupOptions(){
           parent::setupOptions();
           $this->addOptions( array(
                "sizes"       => array( '10' => 10 , '15' => 15 , '25' => 25 , '50' => 50 , '100' => 100 , '0' => 'All' ) ,
                "size"        => 25 ,
                "radius"      => 3  ,
                "summary"     => t('Showing %{number_format:start} to %{number_format:end} of %{number_format:count} records')   ,
           ));
        }

        public function setPage( $page ){
            $this->setState( 'page' , $page );
        }

        public function getPage( $validate = true ){
            $page = $this->getState( 'page' );

            if( $page === null || $page < 0 )
                return 0;
            elseif( $validate && $page >= $this->count )
                return max( $this->count - 1 , 0);
            else
                return $page;
        }

        public function setSize( $size ){
            $this->setState( 'size' , $size );
        }

        public function setSizes( $sizes ){
            $this->setOption( 'sizes' , (array) $sizes );
        }

        public function getSize(){
            $size    = $this->getState('size');
            $sizes   = $this->getSizes();
            $default = $this->getOption( 'size' );

            if( $size === null )
                $size = $default;

            if( in_array( $size , $sizes ) )
                return $size;
            else{

                $rsizes = $sizes;
                rsort( $rsizes );

                foreach( $sizes as $rsize ){
                    if( $rsize <= $size )
                        return $rsize;
                }


                return array_pop( $rsizes );

            }

        }

        public function setCount( $count ){
            $this->count = max( 0 , (int)$count );
        }

        public function getCount(){
            return $this->count;
        }

        public function getRadius(){
            return $this->getOption('radius');
        }

        public function getSizes(){
            return $this->getOption('sizes');
        }

        public function getStart(){
            $page    = $this->getPage();
            $size    = $this->getSize();
            return ($page * $size) + 1;
        }

        public function getEnd(){
            $page    = $this->getPage();
            $size    = $this->getSize();
            $count   = $this->getCount();

            if( !$size )
                return $count;
            else
                return min( ($page+1) * $size , $count );

        }

        public function setUrl( $arguments = null , $url = '.' ){
            $this->url       = $url;
            $this->arguments = $arguments;
        }

        public function getUrl( $page = null , $size = null ){

            $modifiers = array();
            if( $page !== null ) $modifiers['page'] = $page;
            if( $size !== null ) $modifiers['size'] = $size;

            $state     = $this->getState( null , $modifiers , true );
            $url       = $this->url;

            $arguments = array();
            if( $this->arguments ){
                foreach( $this->arguments as $v ){
                    if( array_key_exists( $v , $_GET ) )
                        $arguments[ $v ] = $_GET[ $v ];
                }
            }else{
                $arguments = $_GET;
            }

            foreach( $state as $k => $v ){
                $arguments[$k] = $v;
            }

            $query = http_build_query( $arguments );
            $url   = $this->url . ($query ? '?'.$query : null );

            return url( $url );
        }

        public function getPages(){
            $size  = $this->getSize();
            $count = $this->count;
            if( $size <= 0 )
                return 1;
            else
                return ceil( $count / $size );
        }

        /**
         * Pass a DQL and limit it
        */
		function paginate( &$obj  ){

			//Caluculate the record count
            $obj   = $obj->copy(); //Fix doctrine query problems
			$count = $obj->count();
			$this->setCount( $count );

            //Get the page
            $page = $this->getPage();

            //Do the actual limiting
            $size = $this->getSize();
            if( $size > 0 ){
                $obj->limit( $size );
                $obj->offset( $page * $size );
            }

            return $obj;

		}

       /**
        * Render
        */
       public function render( $what = "pagination" ){

            if( $what == "pagination" ){
                return $this->renderPagination();
            }elseif( $what == "summary" ){
                return $this->renderSummary();
            }elseif( $what == "pager" ){
                return $this->renderPager();
            }else{
            	return '';
            }

       }

       /**
        * Renders Pager
        */
       public function renderPager( ){

       		$output = array();
            $sizes  = $this->getOption('sizes');
            $size   = $this->getSize();

			$output[]  = "<strong>".t("Show: ")."</strong>";
			foreach( $sizes as $i => $caption ){
				if( $size == $i ){
					$output[] = "<strong>";
						$output[] = $caption;
					$output[] = "</strong>";
				}else{
					$output[] = "<a href='".$this->getUrl( 0 , $i )."'>";
						$output[] = $caption;
					$output[] = "</a>";
				}
					$output[] = "&nbsp;";
			}

			return implode( "" , $output );
       }


       /**
        * Renders summary
        */
       public function renderSummary( ){

            $summary = $this->getOption('summary');

            $variables = array();
            $variables["page"]      = $this->getPage();
            $variables["size"]      = $this->getSize();
            $variables["pages"]     = $this->getPages();
            $variables["count"]     = $this->getCount();
            $variables["start"]     = $this->getStart();
            $variables["end"]       = $this->getEnd();

            return t( $summary , $variables );

       }

       /**
        * Renders the pagination
        */
		function renderPagination(){

            $name    = $this->getName();
            $page    = $this->getPage();
            $count   = $this->getPages();
            $radius  = $this->getOption('radius');
            $html    = "";

			$html .= "<div id='$name' class='pagination btn-group'>";

			//Previous
			if( $page <= 0 )
				$html .= "<span class='previous btn btn-primary'> ".t('< Previous')." </span>";
			else
				$html .= "<a href='".$this->getUrl( $page -1 )."' class='previous pagination-link btn btn-primary'> ".t('< Previous')." </a>";

			//Draw first 2 pages
			for( $x = 0 ; $x < min(2,$count); $x++ ){
				if( $page == $x )
					$html .= "<span class='current btn btn-default'>".($x+1)."</span>";
				else
					$html .= "<a href='".$this->getUrl( $x )."' class='pagination-link btn btn-default'>".($x+1)."</a>";
			}

			//Draw ellipsis
			if( max(2 , $page-$radius) != 2)
				$html .= "<span class='ellipsis btn btn-primary'>...</span>";

			//Draw Middle Pages
			for( $x = max(2 , $page-$radius) ; $x < min($count-2,$page+$radius); $x++ ){
				if( $page == $x )
					$html .= "<span class='current btn btn-default'>".($x+1)."</span>";
				else
					$html .= "<a href='".$this->getUrl( $x )."' class='pagination-link btn btn-default'>".($x+1)."</a>";
			}

			//Draw ellipsis
			if( min($count-2,$page+$radius) != $count-2)
				$html .= "<span class='ellipsis btn btn-primary'>...</span>";

			//Draw last 2 pages
			if( $count > 2 )
			for( $x = max(2,$count - 2) ; $x < $count; $x++ ){
				if( $page == $x )
					$html .= "<span class='current btn btn-default'>".($x+1)."</span>";
				else
					$html .= "<a href='".$this->getUrl( $x )."' class='pagination-link btn btn-default'>".($x+1)."</a>";
			}

			//Next
			if( $page >= $count - 1 )
				$html .= "<span class='next btn btn-primary'> ".t('Next >')." </span>";
			else
				$html .= "<a href='".$this->getUrl( $page + 1 )."' class='next pagination-link btn btn-primary'> ".t('Next >')." </a>";

			$html .= "</div>";

			//Draw Next
			return $html;

        }

    }
