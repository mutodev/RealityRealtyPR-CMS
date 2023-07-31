<?php

    class Helper_Menu extends Helper{


        private $menu_label = "";

        private $label       = "";
        private $active      = null;
        private $menu        = array();
        private $max_depth   = 1;
        private $nested      = false;
        private $collapsed   = false;
        private $highlight   = "path";
        private $prefix      = "";
        private $images      = true;
        private $description = false;


        public function count( ){
            return count( $this->menu );
        }

        public function load( $filename ){

            @$yaml = Yaml::load( $filename );
            $this->menu  = $yaml['menu'];
        }

        public function setMenuLabel( $label = null ){
            return $this->label = $label;
        }

        public function getMenuLabel(){
            return $this->label;
        }

        public function getActiveAction(  ){
            return Router::getCurrentAction();
        }

        public function &getItem( $path , $update = null , $delete = false ){

            $path = explode( "." , $path );
            $name = array_pop( $path );

            $menu = &$this->menu;
            foreach( $path as $p ){

                if( !isset( $menu[ $p] )){
                    return null;
                }

                if( !isset( $menu[ $p]['menu'] ) ){
                    return null;
                }

                $menu = &$menu[ $p ]["menu"];

            }

            $item = &$menu[ $name ];

            if( $update ){
                $item = array_merge( $item , $update );
            }

            if( $delete ){
                unset( $menu[ $name ] );
            }

            return $item;

        }

        public function getActionForPath( $path ){

            $item = $this->getItem( $path );
            if( !empty($item['active']) ){
                return $item['active'];
            }elseif( !empty( $item['link'] )){
                return $item['link'];
            }else{
                return $path;
            }

        }

        public function setActive( $active = null ){
            $this->active = $active;
        }

        public function isActive( $path , $mode = "path" ){

            //Get the real path
            $action = $this->getActionForPath( $path );
            $active = $this->getActiveAction();

            if( $this->active == $path ){
                $action = $this->active;
                $active = $this->active;
            }

            //Check if its active
            if( $mode == "path" ){
                $p1     = explode( "." , $action );
                $p2     = explode( "." , $active );
                $p2     = array_slice( explode( "." , $active ) , 0 , count($p1)  );
                return ($p1 == $p2);
            }elseif( $mode == "exact" ){
                return $action == $active;
            }else{
                return false;
            }

        }

        public function isCollapsed( $path ){

            //Get Parent path
            $path = explode( "." , $path );
            array_pop( $path );
            $path = implode( "." , $path );

            if( empty( $path ) ){
                return false;
            }else{
                return !$this->isActive( $path );
            }

        }

        public function argumentMatch( $path ){

             //Check the arguments
             $item  = $this->getItem( $path );
             $args = $item['args'];

             if( empty( $args ) )
                return true;

             foreach( $args as $k => $v ){

                $v     = (array) $v;
                $match = false;

                foreach( $v as $v2 ){
                    if( $v2 === true ){
                        if( get( $k , null ) !== null ){
                            $match = true;
                            break;
                        }
                    }elseif( $v2 === false ){
                        if( get( $k , null ) == null ){
                            $match = true;
                            break;
                        }
                    }else{
                        if( get( $k , null ) == $v2 ){
                            $match = true;
                            break;
                        }
                    }
                }

                if( $match  ){
                    return true;
                }

              }

              return false;

        }

        public function isHighlighted( $path , $mode = null ){

            if( $mode === null )
                $mode = $this->highlight;

            $active = $this->isActive( $path , $mode );
            if( !$active )
                return false;

            //Check the arguments
            if( !$this->argumentMatch( $path ) ){
                return false;
            }

            return true;

        }

        public function isVisible( $path ){

            //Check if we have access
            if( !$this->hasAccess( $path ) ){
                return false;
            }

            //Find the item
            $item = $this->getItem( $path );
            if( empty( $item ) )
                return false;

            //Check if the item is visible
            $visible = $item['visible'];
            if( empty( $visible ) ){
                return false;
            }

            //Check if the visiblity depends
            //if its active
            if( $visible === "active" ){

                //Check if the item is active
                if( !$this->isActive( $path ) ){
                    return false;
                }

                //Check the arguments
                if( !$this->argumentMatch( $path ) ){
                    return false;
                }

            }

            return true;

        }

        public function hasAccess( $path ){
            $item = $this->getItem( $path );
            if( !empty( $item['permission'] ) )
                return Auth::hasPermission( $item['permission']  );

            return true;
        }

        public function add( $path , $labelOrOptions = ''  , $link = '' , $icon = '' , $active = '' , $description = '' , $class = '' ){

            $path = explode( "." , $path );
            $name = array_pop( $path );

            if( is_array( $labelOrOptions ) ){
                $item = $labelOrOptions;
            }else{

                $item = array(
                    "label"       => $labelOrOptions ,
                    "link"        => $link  ,
                    "icon"        => $icon  ,
                    "active"      => $active ,
                    "description" => $description ,
                    "class"       => $class ,
                );

            }

            //Set default options
            $default_options = array(
                "label"       => "" ,
                "link"        => "" ,
                "icon"        => "" ,
                "active"      => "" ,
                "highlight"   => null ,
                "description" => "" ,
                "visible"     => true ,
                "args"        => array() ,
                "permission"  => "" ,
                "class"       => "" ,
            );

            $item = array_merge( $default_options , $item );

            $menu = &$this->menu;
            foreach( $path as $p ){

                if( !isset( $menu[ $p] )){
                    $menu[ $p ] = array();
                }

                if( !isset( $menu[ $p]['menu'] ) ){
                    $menu[ $p]['menu'] = array();
                }

                $menu = &$menu[ $p ]["menu"];

            }

            $menu[ $name ] = $item;

        }

        public function remove( $path ){
            return $this->getItem( $path , null , true );
        }

        public function removeAll(){
            $this->menu = array();
        }

        public function update( $path , $options = array() ){
            return $this->getItem( $path , $options , false );
        }

        public function render( $options = array() ){

            $level             = input( $options , 'level'      , 0 );
            $path              = input( $options , 'path'       , null );
            $this->max_depth   = input( $options , 'max_depth'  , 1 );
            $this->nested      = input( $options , 'nested'     , true );
            $this->collapsed   = input( $options , 'collapsed'  , false );
            $this->highlight   = input( $options , 'highlight'  , 'path' );
            $this->prefix      = input( $options , 'prefix'     , '' );
            $this->images      = input( $options , 'images'     , true );
            $this->description = input( $options , 'description', false );

            //Get the data to render
            $menu      = &$this->menu;
            $menu_path = explode( "." , $path );
            $real_path = array();

            while( $level > 0 ){

                //Get the submenu by Path
                if ( $path ) {
                    $id = array_shift( $menu_path );
                }
                //Get the submenu by Active
                else {
                    foreach( $menu as $key => $value ) {

                        $full_path = trim(implode('.', $real_path) . '.' . $key, '.');

                        if ( $this->isActive( $full_path ) ) {
                            $id = $key;
                            break;
                        }
                    }
                }

                if( $id === null )
                    return '';


                if( !isset( $menu[ $id ]['menu'] ) )
                    return '';

                $menu = &$menu[ $id ]['menu'];
                $real_path[] = $id;
                $level--;
            }

            $real_path = implode( '.', $real_path );

            if( count( $menu ) ){

                $output     = array();
                $output[] = "<div class='yammon-menu'>";
                $menu_label = $this->getMenuLabel();
                if( $menu_label ){
                    $output[] = "<div class='yammon-menu-label'>";
                    $output[] = $menu_label;
                    $output[] = "</div>";
                }

                //Get the menu to render
                $output[] = $this->_render( $real_path , $menu , 0  );
                $output[] = "</div>";
                return implode( "\n" , $output );

            }else{
                return '';
            }

        }

        private function _render( $path , $menu , $level  ){

            $output = array();
            $i      = 0;
            $c      = count( $menu );

            if( $this->nested || $level == 0)
                $output[] = "<ul class=\"site-menu\" data-plugin=\"menu\">";

            $visible_count = 0;
            foreach( $menu as $name => $item ){
                $full_name   = $path ? $path.".".$name : $name;
                if( $this->isVisible( $full_name ) ){
                    $visible_count++;
                }
            }

            foreach( $menu as $name => $item ){

                $full_name   = $path ? $path.".".$name : $name;
                $first       = ( $i == 0   );
                $last        = ( $i == $visible_count-1);
                $href        = !empty($item['link'])  ? url($item['link'])  : '';
                $icon        = !empty($item['icon'])  ? $item['icon']  : '';
                $label       = !empty($item['label']) ? $item['label'] : '';
                $description = !empty($item['description']) ? $item['description'] : '';
                $submenu     = isset($item['menu'])  ? $item['menu']  : array();
                $highlight   = isset($item['highlight'])  ? $item['highlight']  : null;
                $active      = $this->isHighlighted( $full_name , $highlight );
                $uclass      = isset($item['class'])  ? $item['class']  : null;

                //Check if its vissible
                if( !$this->isVisible( $full_name ) ){
                    continue;
                }

                //Generate Classes
                $classes = array();
                if( $first )   $classes[] = "first";
                if( $last  )   $classes[] = "last";
                if( $active  ) $classes[] = "active";
                if( count( $submenu) && $level +1 < $this->max_depth ) $classes[] = "collapsible";

                if( $this->collapsed && $this->isCollapsed( $full_name ) ){
                    $classes[] = "collapsed";
                }

                $classes[] = "level$level";

                if( $uclass ) $classes[] = $uclass;
                $classes = implode( " " , $classes );

                //Create Output
                $output[] = "<li class='$classes'>";

                    if( $href )
                        $output[] = "<a href='$href'>";
                    else
                        $output[] = "<strong>";

                            if( $icon )
                                $output[] = "<i class=\"site-menu-icon $icon\" aria-hidden=\"true\"></i>";

                            if( $level > 0 ){
                                $nlabel  = $this->prefix." ";
                                $label   = $nlabel.$label;
                            }

                            $classTitle = $href ? 'site-menu-title': '';

                            $output[] = "<span class=\"$classTitle\">".$label ."</span>";

                            if( $this->description && $description ){
                                $output[] = "<p>".$description."</p>";
                            }

                    if( $href )
                        $output[] = "</a>";
                    else
                        $output[] = "</strong>";

                    if( count( $submenu) && $level +1 < $this->max_depth )
                        $output[] = $this->_render( $full_name , $submenu , $level + 1  );

                $output[] = "</li>";

                $i++;

            }

            if( $this->nested || $level == 0)
                $output[] = "</ul>";

            return implode( "\n" , $output  );

        }

    }
