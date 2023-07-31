<?php

    abstract class Helper_Form_Element extends Helper{

        protected $parent                 = null;
        protected $depends                = array();
        private   $cache                  = array();

        public function __construct( $name , $options = array() , Helper_Form_Element_Container $parent = null ){

            //Save the parent
            if( $parent !== null ){
                $this->parent = $parent;
            }

            parent::__construct( $name , $options );

            //Call the construct method
            //TODO IS THIS NECESARY?
            $this->construct();

            //Call the build method
            $this->build();

        }

        public function __clone(){
            $this->cache = array();

            $this->construct();
            $this->build();
        }

        public function setupOptions(){
            parent::setupOptions();

            $this->addOption( "tags"          , array() );
            $this->addOption( "attributes"    , array() );
            $this->addOption( "domid"         , null );
            $this->addOption( "label"         , null );
            $this->addOption( "description"   , null );
            $this->addOption( "example"       , null );
            $this->addOption( "visible"       , true );
            $this->addOption( "class"         , null );
            $this->addOption( "style"         , null );
            $this->addOption( "depends"       , array() );

            $this->addOption( "box_renderer"         , null );
            $this->addOption( "label_renderer"       , null );
            $this->addOption( "description_renderer" , null );
            $this->addOption( "error_renderer"       , null );
            $this->addOption( "example_renderer"     , null );

            $this->addOption( "colspan"    , 1 );
            $this->addOption( "colclass"   , null );
            $this->addOption( "colwidth"   , null );
            $this->addOption( "colheight"  , null );
            $this->addOption( "colalign"   , null );
            $this->addOption( "colvalign"  , null );
            $this->addOption( "colstyle"   , null );
            $this->addOption( "rowspan"    , 1 );


        }

        /**
        *  construct
        *
        * @return void
        */
        public function construct(){

        }

        /**
        *  build
        *
        * @return void
        */
        public function build(){

        }

       /**
        * Get Label
        *
        * @return string
        */
        public function getLabel(){
            return $this->getOption('label');
        }

        public function getDescription(){
            return t($this->getOption('description'));
        }

        public function getExample(){
            return t($this->getOption('example'));
        }

       /**
        * Get the dom id
        *
        * @return string
        */
        function getDomId(){

            //If specified return it
            $domid = $this->getOption("domid" , null );
            if( $domid ){
                return $domid;
            }

            //TODO: Support for repeating elements
            $full_name    = $this->getParentNames();
            $full_name[]  = $this->getName();
            $full_name    = implode( "_" , $full_name );
            $full_name    = strtolower( $full_name );
            return $full_name;
        }

        function getRelative($path){

            $element = preg_replace('/(?:\.+)?(.+)/', '$1', $path);

            if ( substr($path, 0, 1 ) == '.' ) {

                $count  = strlen( preg_replace('/(\.+)?(?:.+)/', '$1', $path) );
                $parent = $this->getParent();

                for ($i = 1; $i < $count; $i++) {
                    if ( !$parent = $parent->getParent() ) {
                        return null;
                    }
                }

            }
            else {
                $parent = $this->getForm();
            }

            if( $parent )
                return $parent->getElement( $element );
            else
                return null;

        }

        function getSiblings(){

            if( $parent = $this->getParent() ){
                $siblings = $parent->getElements();
                unset( $siblings[ $this->getName() ] );
            }else{
                $siblings = array();
            }

            return $siblings;
        }

        function getPrevious(){

            $parent   = $this->getParent();
            $elements = $parent ? $parent->getElements() : array();
            $previous = null;

            foreach( $elements as $element ) {
                if ( $element == $this )
                    return $previous;
                $previous = $element;
            }

            return null;
        }

        function getNext(){

            $parent   = $this->getParent();
            $elements = $parent ? $parent->getElements() : array();
            $next     = false;

            foreach( $elements as $element ) {

                if( $next === true )
                    return $element;

                if ( $element == $this )
                    $next = true;
            }

            return null;
        }

       /**
        * Alias of Render
        *
        * @return string
        */
        public function __toString(){
            return $this->render();
        }

        /**
         * get the parent for this object
         *
         * @return Helper_Form_Element_Container
        */
        public function getParent(){
            return $this->parent;
        }

        /**
         * get a reference to the top most parent
         *
         * @return Helper_Form_Element_Container
        */
        public function getTopParent(){
            $parents = $this->getParents();
            return array_shift( $parents );
        }

        /**
         * get a reference to the top form ( if exists )
         *
         * @return Helper_Form_Element_Container
        */
        public function getForm(){

            if( isset( $this->cache[ __FUNCTION__ ] ) )
                return $this->cache[ __FUNCTION__ ];

            $parent = $this->getTopParent();
            if( !($parent instanceof Helper_Form) ){
                $parent = null;
            }

            return $this->cache[ __FUNCTION__ ] = $parent;

        }

        /**
         * get a reference to all parents
         *
         * @return Helper_Form_Element_Container
        */
        public function getParents(){

            if( isset( $this->cache[ __FUNCTION__ ] ) )
                return $this->cache[ __FUNCTION__ ];

            $parents = array();
            $parent  = $this;
            while( $parent = $parent->getParent() ){
                $parents[] = $parent;
            }
            $parents = array_reverse( $parents );

            return $this->cache[ __FUNCTION__ ] = $parents;

        }

        /**
         * get an array with all the parents names
         *
         * @return Helper_Form_Element_Container
        */
        public function getParentNames(){

            if( isset( $this->cache[ __FUNCTION__ ] ) )
                return $this->cache[ __FUNCTION__ ];

            $names   = array();
            $parents = $this->getParents();
            foreach( $parents as $parent ){
                $names[] = $parent->getName();
            }
            return $this->cache[ __FUNCTION__ ] = $names;

        }

        /**
         * get the full name for this object
         *
         * @return string
        */
        public function getFullName(){

            if( isset( $this->cache[ __FUNCTION__ ] ) )
                return $this->cache[ __FUNCTION__ ];

            $full_name   = $this->getParentNames();
            $full_name[] = $this->getName();
            return $this->cache[ __FUNCTION__ ] = implode( "." , $full_name );
        }


        public function onFormValueChanged( $event ){

            $subject  = $event->getSubject();
            $myname   = $subject->getDomNamePath();
            $Form     = $this->getForm();

            foreach( $this->depends as $key => $options ){
                $element = $this->getRelative( $key );
                if( !$element ) continue;

                if( $myname == $element->getDomNamePath() ){
                    unset( $this->cache['areDependsSatisfied'] );
                    break;
                }
            }

        }

        /**
         * Checks if this depends are satisfied
         *
         * @return boolean       is the depends are satisfied
        */
        public function areDependsSatisfied( $recursive = true ){

            //Check in the cache
            if( isset( $this->cache[ __FUNCTION__ ] ) )
                return $this->cache[ __FUNCTION__ ];

            //Check parent for dependecies
            $parent = $this->getParent();
            if( $recursive && $parent && !$parent->areDependsSatisfied() ){
                return false;
            }

            //Check if it has depends
            if( empty( $this->depends ) ){
                return $this->cache[ __FUNCTION__ ] = true;
            }

            //Get the form
            $Form = $this->getForm();

            //Check normally
            foreach( $this->depends as $depend_name => $depend_options ){

                $values   = $depend_options['values'];
                $positive = $depend_options['positive'];
                $callback = $depend_options['callback'];

                //Get the element
                $element  = $this->getRelative( $depend_name );

                //Make sure it exits
                if( !$element ){
                    return false;
                }

                //Get the value
                $element_value = $element->getValue( );

                //Check if is satified
                if( $callback ){

                    $return = (bool)(call_user_func( $callback , $this , $element_value ));

                    if( $positive ){
                        if( !$return ){
                            return $this->cache[ __FUNCTION__ ] = false;
                        }
                    }else{
                        if( $return ){
                            return $this->cache[ __FUNCTION__ ] = false;
                        }
                    }

                }elseif( count( $values ) == 0 ){ //Check if the value is truthy
                    if( $positive ){
                        if( !$element_value ) {
                            return $this->cache[ __FUNCTION__ ] = false;
                        }
                    }else{
                        if( $element_value ) {
                            return $this->cache[ __FUNCTION__ ] = false;
                        }
                    }
                }elseif( is_array( $element_value ) ){

                    if( $positive ){
                        if( empty( $element_value ) || !array_intersect( $element_value , $values ) ){
                            return $this->cache[ __FUNCTION__ ] = false;
                        }
                    }else{
                        if( !empty( $element_value ) && array_intersect( $element_value , $values  ) ){
                            return $this->cache[ __FUNCTION__ ] = false;
                        }
                    }

                }else{

                    if( $positive ){
                        if( !in_array( $element_value , $values ) ){
                            return $this->cache[ __FUNCTION__ ] = false;
                        }
                    }else{
                        if( in_array( $element_value , $values  ) ){
                            return $this->cache[ __FUNCTION__ ] = false;
                        }
                    }

                }

            }

            //If we got here all depends were satisfied
            return $this->cache[ __FUNCTION__ ] = true;

        }

        /**
          * Add a new depends
          *
         * @param  Element|string $element_name the name of the element this element depends on
         * @param  array |string  $values       the values for the depends
         * @param  bool           $positive     if the depends is positive
         * @return array
        */
        public function addDepend( $element_name , $options ){

            //Delete cache
            unset( $this->cache['areDependsSatisfied'] );

            //Start Observing Form Value Changes
            if( !$this->observing_form_changes ){
                Event::connect( "form.value.changed" , array( $this , "onFormValueChanged" ) );
                $this->observing_form_changes = true;
            }

            //Get the data from the options
            $values   = array();
            $positive = true;
            $callback = null;

            if( is_array( $options) ){

                $is_numeric = true;
                foreach( $options as $k => $v )
                    if( !is_numeric( $k ) ){
                        $is_numeric = false;
                        break;
                    }

                if( $is_numeric ){
                    $values = $options;
                }else{

                    if( isset( $options['values'] ) )
                        $values = (array) $options['values'];

                    if( isset( $options['positive'] ) )
                        $positive = (bool)$options['positive'];

                    if( isset( $options['callback'] ) )
                        $callback = $options['callback'];
                }

            }else{
                $values = (array) $options;
            }

            $depend                        = array();
            $depend['values']              = $values;
            $depend['positive']            = $positive;
            $depend['callback']            = $callback;

            $this->depends[ $element_name  ] = $depend;

            //Return it
            return $depend;

        }

        /**
          * Add multiple depends at once
          *
          * @return array
        */
        public function addDepends( $depends ){

            //Delete previous depends
            $this->depends = array();

            if( is_string( $depends ) ){
                $depends = array( $depends => null );
            }

            foreach( $depends as $name => $options ){
                $this->addDepend( $name , $options );
            }

            return $this->depends;

        }

        /**
          * Get dependencies
          *
          * @return array
        */
        public function getDependencies(){
            return $this->depends;
        }

        /**
         * Set if an element is visible
         *
         * @return boolean  is the element visible
        */
        public function setVisible( $bool = true ){
            $this->setOption( "visible" , !!$bool );
        }

        /**
         * Checks if the element is visible
         *
         * @return boolean  is the element visible
        */
        public function isVisible(){

            $visible = !!$this->getOption("visible");

            if ($visible) {
                $visible = $this->areDependsSatisfied(false);
            }

            return $visible;
        }

        public function render($options = array()){

        }

        public function renderBox( $options = array() ){
            list( $type , $options ) = $this->getRendererOptions( 'Box' , $options );
            return Helper_Form_Renderer_Box::factory( $type )->render( $this , $options );
        }

        public function renderDescription(  $options = array() ){
            list( $type , $options ) = $this->getRendererOptions( 'Description' , $options );
            return Helper_Form_Renderer_Description::factory( $type )->render( $this , $options );
        }

        public function renderError( $options = array() ){
            list( $type , $options ) = $this->getRendererOptions( 'Error' , $options );
            return Helper_Form_Renderer_Error::factory( $type )->render( $this , $options );
        }

        public function renderExample( $options = array() ){
            list( $type , $options ) = $this->getRendererOptions( 'Example' , $options );
            return Helper_Form_Renderer_Example::factory( $type )->render( $this , $options );
        }

        public function renderLabel( $options = array() ){
            list( $type , $options ) = $this->getRendererOptions( 'Label' , $options );
            return Helper_Form_Renderer_Label::factory( $type )->render( $this , $options );
        }

        protected function getRendererOptions( $type , $options ){

            //Check the cache
            if( isset( $this->cache[__FUNCTION__][ $type ] ) )
                return $this->cache[__FUNCTION__][ $type ];

            //Get the option name
            $option_name = strtolower( $type )."_renderer";

            //Get default options
            $default_options = array();
            if( $parent = $this->getParent() )
                $default_options = $parent->getDefaultRendererOptions( $type );

            //Get element options
            $element_options = $this->getOption( $option_name );
            if( $element_options === null )
                $element_options = array();
            elseif( !is_array( $element_options ) )
                $element_options = array(
                    'type' => (string)$element_options
                );

            //Get Passed options
            if( $options === null )
                $options = array();
            elseif( !is_array( $options ) )
                $options = array(
                    'type' => (string)$options
                );

            //Merge options
            $options = array_merge( $default_options , $element_options , $options );

            //Save into the cache
            $this->cache[__FUNCTION__][ $type ] = $options;

            //Extract subtype
                $sub_type = @$options['type'];
            $sub_type = null;
            if( isset( $options['type'] ) ){
                $sub_type = $options['type'];
                unset( $options['type'] );
            }

            return array( $sub_type , $options );

        }

        protected function getDefaultRendererOptions( $type ){

            if( isset( $this->cache[__FUNCTION__][ $type ] ) ){
                return $this->cache[__FUNCTION__][ $type ];
            }

            //Get the option name
            $option_name = strtolower( $type )."_renderer";

            //Get the parent options
            $parent_options = array();
            if( $parent = $this->getParent() ){
                $parent_options = $parent->getDefaultRendererOptions( $type );
            }

            //Get the element options
            $element_options = $this->getOption('default_renderers');
            $element_options = isset( $element_options[ $option_name ] ) ? $element_options[ $option_name ] : null;
            if( $element_options === null )
                $element_options = array();
            elseif( !is_array( $element_options ) )
                $element_options = array(
                    'type' => (string)$element_options ,
                );


            //Merge the options
            $options = array_merge( $parent_options , $element_options );

            return $this->cache[__FUNCTION__][$type] = $options;

        }

        /**
         * Override Mixin Options setOptions
         * to automatically add elements, filters, validations and warnings
         *
         * @param $options             and array of $option_name => $option_value pairs
         * @return array
        */
        public function setOptions( $options, $overwrite = true ){

            $options = parent::setOptions( $options, $overwrite );

            if( isset( $options['depends'] ) ){
                $this->addDepends( $options['depends'] );
            }

            return $options;
        }

        /**
            Add Attribute
        **/
        public function addAttribute( $name , $value ){

            if( $name == 'style' )
                $this->addStyle( $value );
            elseif( $name == 'class' )
                $this->addClass( $value );
            else{
                $attributes = (array)$this->getOption( 'attributes' );
                $attributes[ $name ] = $value;
                $this->setOption( 'attributes' , $attributes );
            }

        }

        /**
            Add Attributes
        **/
        public function addAttributes( $values ){

            //Reset Attributes
            $this->setOption('attributes' , array() );

            $values = (array) $values;
            foreach( $values as $k => $v ){
                $this->addAttribute( $k , $v );
            }
        }

        /**
            Add Class
        **/
        public function addClass( $class ){
            $org     = (array)$this->getOption( 'class' );
            $classes = (array)$this->getOption( 'class' );

            if( is_array( $class ) )
                foreach( $class as $k => $v )
                    $classes[] = $v;
            else
                $classes[ ] = $class;

            $this->setOption( 'class' , $classes );
        }

        /**
            Add Classes
        **/
        public function addClasses( $classes ){

            //Reset Classes
            $this->setOption( 'class' , array() );

            $classes = (array) $classes;
            foreach( $classes as $k => $v ){
                $this->addClass( $v );
            }

        }

        /**
            Add Style
        **/
        public function addStyle( $name , $value = null ){
            $styles = (array)$this->getOption( 'style' );

            if( is_array( $name ) )
                foreach( $name as $k => $v )
                    $styles[ $k ] = $v;
            else
                $styles[ $name ] = $value;

            $this->setOption( 'style' , $styles );
        }

        /**
            Add Styles
        **/
        public function addStyles( $styles ){

            //Reset Styles
            $this->setOption('style' , array() );

            $styles = (array) $styles;
            foreach( $styles as $k => $v ){
                $this->addStyle( $k , $v );
            }

        }

        /**
            Get Attributes
        **/
        public function getAttributes( $array = false , $classes = true , $style = true ){

           $attributes = (array)$this->getOption('attributes' , array() );

           if( $classes ){
               $return       = $this->getClasses( $array );
               if( $return ) $attributes['class'] = $this->getClasses( $array );
           }else{
               unset( $attributes['class'] );
           }

           if( $style ){
               $return       = $this->getStyles( $array );
               if( $return ) $attributes['style'] = $this->getStyles( $array );
           }else{
               unset( $attributes['style'] );
           }

           if( $array )
               return $attributes;

            $return = array();
            foreach( $attributes as $k => $v ){

                if( $v === null )
                    continue;

                if( is_numeric( $k ) )
                    $return[] = "$v";
                else
                    $return[] = "$k='$v'";
            }

            return implode( " " , $return );

        }

        protected function prepareAttributes( $attributes ){


        }

        /**
            Get Classes
        **/
        public function getClasses( $array = false ){
            $classes = (array)$this->getOption('class');
            if( $array )
                return $classes;
            else
                return implode( " " , $classes );
        }

        /**
            Get Styles
        **/
        public function getStyles( $array = false ){

            $styles = (array)$this->getOption('style');
            foreach( $styles as $k => $v ){

                if( $v === null )
                    unset( $styles[$k] );

                if( is_numeric( $k ) ){

                    $split = explode( ";" , $v );
                    foreach( $split as $k2 => $v2 ){
                        $split2 = explode( ":" , $v2 );
                        $k3     = @$split2[0];
                        $v3     = @$split2[1];
                        if( $k3 && $v3 )
                            $styles[trim($k3)] = trim($v3);
                    }
                    unset( $styles[ $k ] );
                }

            }
            if( $array )
                return $styles;

            $return = array();
            foreach( $styles as $k => $v ){
                $return[] = "$k:$v";
            }

            return implode( ";" , $return );
        }

        public function getNested(){
            return array();
        }

        public function getTranslationStrings(){

            $strings   = array();

            $label = $this->getLabel();
            if( $label ) $strings[] = $label;

            $description = $this->getDescription();
            if( $description ) $strings[] = $description;

            $example = $this->getExample();
            if( $example ) $strings[] = $example;

            return $strings;

        }

        public function getTags()
        {
            $tags = (array) $this->getOption('tags');
            return $this->cleanTags($tags);
        }

        public function hasTag( $tags, $all = false )
        {
            $currentTags = $this->getTags();
            $tags        = $this->cleanTags( $tags );
            $count       = count( array_intersect($currentTags, $tags) );

            if ( $count === 0 )
                return false;

            else if ( $all == true && $count != count($tags) )
                return false;

            return true;
        }

        public function setTag( $tags = array() )
        {
            $this->setOption('tags', $this->cleanTags($tags) );
        }

        public function addTag( $tags )
        {
            $currentTags = $this->getTags();
            $tags        = $this->cleanTags( $tags );

            foreach( $tags as $tag )
                $currentTags[] = $tag;

            $this->setTag($currentTags);
        }

        public function removeTag( $tags )
        {
            $tags = array_diff( $this->getTags(), $this->cleanTags($tags) );

            $this->setTag( $tags );
        }

        protected function cleanTags( $tags )
        {
            //Convert to Array
            $tags = (array) $tags;

            //Convert to Lower Cases
            foreach( $tags as &$tag )
                $tag = trim(strtolower($tag));

            $tags = array_unique( $tags );
            $tags = array_values( $tags );

            return $tags;
        }

        public function destroy()
        {
            $parent = $this->getParent();
            if( $parent )
                return $parent->removeElement( $this );
            else
                return null;

        }

    }
