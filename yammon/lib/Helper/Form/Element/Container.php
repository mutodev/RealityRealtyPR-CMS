<?php

    class Helper_Form_Element_Container extends Helper_Form_Element_Valued implements Iterator , ArrayAccess , Countable{

        protected      $elements = array();
        static private $uid      = 0;

        private $name_to_class = array(
            "/^(id)$/i"            => "Hidden"   ,
            "/\b(website)\b/i"     => "Website"      ,
            "/\b(cell)\b/i"        => "Phone"    ,
            "/\b(cellphone)\b/i"   => "Phone"    ,
            "/\b(fax)\b/i"         => "Phone"    ,
            "/\b(description)\b/i" => "TextArea" ,
            "/\b(notes)\b/i"       => "TextArea"
        );

        public function setupOptions(){
            parent::setupOptions();

            $this->addOption( "collect_errors"     , false );
            $this->addOption( "present"            , null );
            $this->addOption( "present_all"        , false );
            $this->addOption( "layout_renderer"    , null );
            $this->addOption( "default_renderers"  , null );
            $this->addOption( "box_renderer"       , null );
            $this->setOption( "label"              , ""   );

/*
            $this->addOption( "name"       , null , true );
            $this->addOption( "columns"    , 3 );
            $this->addOption( "attributes"  );
            $this->addOption( "default_label_position" );
            $this->addOption( "collect_errors" , true );
            $this->addOption( "present"        , null );
*/

        }

        public function __clone( ){
            parent::__clone();

            $this->cache = array();

            foreach( $this->elements as $k => $element ){
                $clone         = clone( $element );
                $clone->parent = $this;
                $this->elements[ $k ] = $clone;
            }
        }

       /**
        * Magic isset Method
        *
        *
        * @param $key the element to be checked
        * @return boolean determines if the key exists
        */
        public function __isset( $key ){
            return $this->offsetExists( $key );
        }

       /**
        * Magic unset Method
        *
        *
        * @param $key the element to be unset
        */
        public function __unset( $key ){
            $this->offsetUnset( $key );
        }

        /**
         * setOptions
         *
         * @param $options             and array of $option_name => $option_value pairs
         * @return array
        */
        public function setOptions( $options, $overwrite = true ){

            $options = parent::setOptions( $options, $overwrite );

            if( isset( $options['elements'] ) ){

                $elements = $options['elements'];
                if( is_string( $elements ) ){
                    $elements = realpath( $elements );
                    if( FS::isFile( $elements ) )
                        $elements = Yaml::load( $elements );
                    else
                        $elements = array();
                }

                $this->addElements( $elements );
            }

      return $options;

        }

        /**
         * Alias for addElement
         *
        */
        public function add( $element , $position = 'after' , $reference = null ){
            return $this->addElement( $element , $position , $reference );
        }

        protected function guessElementType( $name ){

            //Check by the name of the column
            foreach( $this->name_to_class as $regex => $type ){
                if( preg_match( $regex , $name ) )
                    return $type;
            }

            //Check if there is an element with an exact name
            $class = Inflector::classify( $name );
            $full_class = "Helper_Form_Element_$class";
            if( class_exists( $full_class ) ){
                return $class;
            }

            return 'Text';

        }


        /**
         * Add an element to the container
         *
         * @param  array | Helper_Form_Element $element_name      the element to add
         * @return Helper_Form_Element
        */
        public function addElement( $element , $position = 'after' , $reference = null ){

            if( $element instanceof Helper_Form_Element ){

                $this->cache  = array();

                //Remove From Parent
                $parent = $element->getParent();
                if( $parent !== $this ){
                    $parent->removeElement( $element );
                }

                //Clone and adopt
                $object = clone($element);
                $object->parent = $this;


            }else{

                if( !is_array( $element ) ){
                    $name    = $element;
                    $type    = 'Text';
                    $options = array();
                }else{
                    $name  = isset( $element['name'] ) ? $element['name'] : 'element_'.(self::$uid++);
                    $type  = isset( $element['type'] ) ? $element['type'] :  $this->guessElementType( $name );
                    unset( $element['name'] );
                    unset( $element['type'] );
                    $options = $element;
                }

                $type   = Inflector::classify( $type );
                $class  = "Helper_Form_Element_$type";
                if( !class_exists( $class ) ){
                    throw new Exception("Unknown Element $type");
                }

                $object = new $class( $name , $options , $this );

            }

            /* TODO: Use the reference parameter */
            switch( $position ){
                case 'before':
                                $elements = array();
                                $elements[ $object->getName() ] = $object;
                                foreach( $this->elements as $name => $element ){
                                    $elements[ $name ] = $element;
                                }
                                $this->elements = $elements;
                                break;
                case 'after' :
                default:
                                $this->elements[ $object->getName() ] = $object;
                                break;

            }

            return $object;

        }

        /**
         * Add serveral elements to the container at once
         *
         * @param  array $elements   an array of Helper_Form_Element or a $element_name => $element_options array
         * @return array
        */
        function addElements( $elements ){

            //Clear Previous Elements
            $this->elements = array();


            //Normalize Elements
            if( !is_array( $elements ) )
                $elements = array( $elements );

            //Add Elements
            foreach( $elements as $name => $element ){

                //Add the name to the element
                if( !($element instanceof Helper_Form_Element) ){
                    $element = (array) $element;
                    $element['name'] = $name;
                }

                $this->addElement( $element );
            }

        }

        /**
         * Alias for removeElement
         *
        */
        public function remove( $element_name ){
            return $this->removeElement( $element_name );
        }

        /**
         * Remove an element from the container
         *
         * @param  string $element_name     the name of the element to add or an instance of an element
         * @return Helper_Form_Element or null on failure
        */
        public function removeElement( $element_name ){

              unset( $this->cache['getElement'] );

              static $id = 0;
                $id++;

              if( $element_name instanceof Helper_Form_Element ){

                foreach( $this->elements as $k => $element ){
                    if( $element == $element_name ){
                        $this->elements[$k]->parent = null;
                        unset( $this->elements[ $k ] );
                        return $element;
                    }
                }

                return null;

              }else{

                  $name     = $this->getName();
                  $path     = explode( "." , $element_name );
                  $key      = array_pop( $path );
                  $elements = &$this->elements;

                  if( isset( $path[0] ) && $path[0] == $name ){
                     array_shift( $path );
                  }

                  foreach( $path as $p ){
                      if( isset( $elements[ $p ] ) )
                        $elements = &$elements[ $p ]->elements;
                      else
                        return null;
                  }

                  $element = null;
                  if( isset( $elements[$key] ) ){
                     $element         = $elements[$key];
                     $element->parent = null;
                     unset( $elements[$key] );
                  }

                  return $element;
              }



        }

        public function removeElements(){
            foreach( $this->elements as $element ){
                $this->removeElement( $element );
            }
        }

        /**
         * Checks if the form has an element with that name
         *
         * @param  string $element_name  the name of the element to check for
         * @return boolean
        */
        public function hasElement( $element_name ){
            $element = $this->getElement( $element_name );
            return (bool)$element;
        }

        /**
         * Alias for getElement
         *
         * @param  string $element_name  the name of the element to get
         * @return Helper_Form_Element
        */
        public function get( $element_name ){
            return $this->getElement( $element_name );
        }

        /**
         * Get an element
         *
         * @param  string $element_name  the name of the element to get
         * @return Helper_Form_Element
        */
        public function getElement( $element_name ){

          if( isset($this->cache[ __FUNCTION__ ][ $element_name ] ) ){
              return $this->cache[ __FUNCTION__ ][ $element_name ];
          }else{
              $this->cache[ __FUNCTION__ ] = array();
          }

          $name     = $this->getName();
          $path     = explode( "." , $element_name );
          $key      = array_pop( $path );
          $elements = $this->elements;

          if( isset( $path[0] ) && $path[0] == $name ){
             array_shift( $path );
          }

          foreach( $path as $p ){
              if( isset( $elements[ $p ] ) )
                $elements = $elements[ $p ]->elements;
              else
                return null;
          }

          if( !isset( $elements[$key] ) ) {
              return $this->cache[ __FUNCTION__ ][ $element_name ] = null;
          }else{
              return $this->cache[ __FUNCTION__ ][ $element_name ] = $elements[ $key ];
          }

        }

        /**
         * Get all elements
         *
         * @return array
        */
        public function getElements( ){
            return $this->elements;
        }

        /**
         * Get elements by tags
         *
         * @return array
        */
        public function getElementsByTag( $tags, $all = false )
        {
            $tags   = (array) $tags;
            $return = array();
            foreach( $this->elements as $element ){

                if( $element->hasTag( $tags, $all ) ){
                    $return[] = $element;
                }

                if( $element instanceof Helper_Form_Element_Container ){
                    $sub_elements = $element->getElementsByTag( $tags, $all );
                    foreach( $sub_elements as $sub_element ){
                        $return[] = $sub_element;
                    }
                }
            }

            return $return;
        }

        /**
         * Get elements for a value
         *
         * TODO: INCORRECT VALUES FOR SUBFORMS
         * @return Helper_Form_Element | null
        */
        public function getElementForValue( $value )
        {
            $return = null;

            //Convert value
            $value      = !is_array( $value) ? explode( "." , $value ) : $value;
            $key        = array_pop( $value );
            $parent_key = implode( "." , $value );
            $parent     = $parent_key ? $this->getElement( $parent_key ) : $this;

            //Check if the parent is valid
            if( !( $parent instanceof Helper_Form_Element_Container) )
                return null;

            //Check nested containers ( in reverse order )
            $elements = array_reverse( $parent->elements );
            foreach( $elements as $element ){

                if( $element instanceof Helper_Form_Element_SubForm ){
                    continue;
                }

                if( $element instanceof Helper_Form_Element_Container ){
                    $sub_element = $element->getElementForValue( $key );
                    if( $sub_element ) return $sub_element;
                }

            }

            //Check not containers
            $return = isset( $elements[ $key ] ) ? $elements[ $key ] : null;
            if( !($return instanceof Helper_Form_Element_Valued) )
                $return = null;

            return $return;
        }

        public function getValuedElements()
        {
            $elements = array();
            foreach( $this->elements as $element ){

                if( $element instanceof Helper_Form_Element_SubForm ){
                    $elements[ $element->getName() ] = $element;
                }elseif( $element instanceof Helper_Form_Element_Container ){
                    $subelements = $element->getValuedElements();
                    foreach( $subelements as $subelement ){
                        unset($elements[ $subelement->getName() ] );
                        $elements[ $subelement->getName() ] = $subelement;
                    }
                }elseif( $element instanceof Helper_Form_Element_Valued ){
                    $elements[ $element->getName() ] = $element;
                }

            }

            return $elements;

        }

        /**
         * Reset the elements
         *
         * this removes any errors and restores the element
         * as if no value was passed or posted
         *
         * @return void
        */
        public function reset(){
            $this->error   = null;
            $this->warning = null;
            foreach( $this->elements as $element ){
                if( $element instanceof Helper_Form_Element_Valued )
                    $element->reset();
            }
        }

        public function resetValue(){
            foreach( $this->elements as $element ){
                if( $element instanceof Helper_Form_Element_Valued )
                    $element->resetValue();
            }
        }

        protected function normalizeValue( $element_or_values , $element_value = null ){

            $num_args = func_num_args();

            //Normalize Values
            if( is_array( $element_or_values ) )
                return $element_or_values;

            if( is_object( $element_or_values )  && method_exists( $element_or_values , 'toArray' ) )
                return $element_or_values->toArray();

            if( is_object(  $element_or_values ) )
                return (array) $element_or_values;

            if( is_scalar( $element_or_values ) )
                return array( $element_or_values => $element_value );

            return array();

        }

        /**
          * Set the value of an element
          *
          * you can also pass an array as the first element
          * and it behaved like setValues
          *
          * @param string | array $element_name       an array of values or an element name
          * @param mixed $value                       the value to set
          * @return mixed                             the value to set
        */
        public function setValue( $element_or_values , $element_value = null  ){

            $values = $this->normalizeValue( $element_or_values , $element_value );

            foreach( $values as $k => $v )
                if( $element = $this->getElement( $k ) )
                    if( $element instanceof Helper_Form_Element_Valued ){
                        $element->setValue( $v );
                        unset( $values[$k] );
                    }

            if( count( $values ) ){
                foreach( $this->getElements() as $element ){
                    if(!($element instanceof Helper_Form_Element_SubForm) &&
                        ($element instanceof Helper_Form_Element_Container) ){
                        $element->setValues( $values );
                    }
                }
            }

        }

        /**
          * Set the values of all elements
          *
          * alias for setValue
          *
          * @return mixed
        */
        public function setValues( $values ){
            return $this->setValue( $values );
        }

        /**
          * Set the value of an element
          *
          * you can also pass an array as the first element
          * and it behaved like setValues
          *
          * @param string | array $element_name       an array of values or an element name
          * @param mixed $value                       the value to set
          * @return mixed                             the value to set
        */
        public function setDefaultValue( $element_or_values = null , $value = null ){

            $values = $this->normalizeValue( $element_or_values , $value );

            foreach( $values as $k => $v )
                if( $element = $this->getElement( $k ) )
                    if( $element instanceof Helper_Form_Element_Valued ){
                        $element->setDefaultValue( $v );
                        unset( $values[$k] );
                    }

            if( count( $values ) ){
                foreach( $this->getElements() as $element ){
                    if(!($element instanceof Helper_Form_Element_SubForm) &&
                        ($element instanceof Helper_Form_Element_Container) ){
                        $element->setValues( $values );
                    }
                }
            }

        }



        public function setDefaultValues( $values ){
            return $this->setDefaultValue( $values );
        }

        /**
          * Get the value of an element
          *
          * if $element_name is not passed it behaves exactly like getValues
          *
          * @param string                             the element to get the value
          * @return mixed                             the value or $default if it was not set or the element doesn't exist
        */
        public function getValue( $element_name = null ){

            //If $element_name is specified return its value
            if( $element_name != null ){

                $element = $this->getElement( $element_name );
                if( $element instanceof Helper_Form_Element_Valued )
                    return $element->getValue();
                else
                    return null;

            }

            $values = array();
            foreach( $this->elements as $element_name => $element ){

                if( !($element instanceof Helper_Form_Element_Valued) )
                    continue;
                elseif( $element instanceof Helper_Form_Element_Subform )
                    $values[ $element_name ] = $element->getValue();
                elseif( $element instanceof Helper_Form_Element_Container ){
                    $sub_values = (array)$element->getValues( );
                    foreach( $sub_values  as $k => $v )
                        $values[ $k ] = $v;
                }else{
                    $values[ $element_name ] = $element->getValue( );
                }

            }

            return $values;

        }

        /**
          * Get the values of all elements
          *
          * @return mixed                an array of $element_name => $value pairs
        */
        public function getValues( ){
            return $this->getValue();
        }

        /**
          * Get the unfiltered value of an element
          *
          * if $element_name is not passed it behaves exactly like getUnfilteredValues
          *
          * @param string                             the element to get the value
          * @return mixed                             the value or $default if it was not set or the element doesn't exist
        */
        public function getUnfilteredValue( $element_name = null  ){

            //If $element_name is specified return its value
            if( $element_name != null ){

                $element = $this->getElement( $element_name );
                if( $element instanceof Helper_Form_Element_Valued )
                    return $element->getUnfilteredValue();
                else
                    return null;

            }

            //Gather the values from the elements
            $values = array();
            foreach( $this->elements as $element_name => $element ){

                if( !($element instanceof Helper_Form_Element_Valued) )
                    continue;
                elseif( $element instanceof Helper_Form_Element_Subform )
                    $values[ $element_name ] = $element->getUnfilteredValue();
                elseif( $element instanceof Helper_Form_Element_Container ){
                    $sub_values = (array)$element->getUnfilteredValue( );
                    foreach( $sub_values  as $k => $v )
                        $values[ $k ] = $v;
                }else{
                    $values[ $element_name ] = $element->getUnfilteredValue( );
                }

            }

            return $values;

        }


        /**
          * Get the unfiltered values for all elements
          *
          * @return mixed                an array of $element_name => $value pairs
        */
        public function getUnfilteredValues( ){
            return $this->getUnfilteredValue();
        }

        /**
         * Checks if this element can be considered present
         * this is meant to be redifined in sub classes
         *
         * @param  $default       if the element is equal to the default should it be considered present
         * @return array          an array with all the errors indexed by the element name
        */
        protected function isPresent( $value ){

            if( !is_array( $value ) )
                return false;

            //Get the Elements to check presence
            $present_elements = (array)$this->getOption('present');
            $present_all      = $this->getOption('present_all');

            //Get Check Elements
            $check_elements = array();
            if( empty( $present_elements ) ){
                $check_elements = $this->getValuedElements();
            }else{
                foreach( $present_elements as $k => $v ){
                    $check_elements[ $k ] = $this->getElement( $v );
                }
            }

            //Verify Presence
            $check_count = 0;
            $elements    = $this->getValuedElements();

            foreach( $elements as $element_name => $element ){

                //Skip some elements
                if( !in_array( $element , $check_elements , true ) )
                    continue;

                //Check presence
                $v = input( $value , $element_name , null , 'none' );
                if( $element->isPresent( $v ) ){
                    $check_count++;
                }

            }

            if( $present_all )
                return $check_count >= count( $check_elements );
            else
                return (bool) $check_count;


        }

        /**
         * Checks if this element is valid
         * after this method is called errors and warnings will be available
         *
         * @return boolean
        */
        public function isValid( $skip = array() ){

            $skip = (array) $skip;

            //Check if this element was invalidated
            if( !empty( $this->error ) )
                return false;

            //Check required
            $required = $this->isRequired();

            if( $required ){

                $value    = $this->getValue();
                $present  = $this->isPresent( $value );

                //Check if its required
                if( !$present ){

                  $message  = $this->getRequiredMessage();
                  $options = $this->getOptions();
                  $options['label'] = $this->getLabel();

                  $template    = new Template( $message );
                  $this->error = $template->apply( $options );
                  return false;
                }

            }

            //Check built in validation
            $error = $this->validate();
            if( $error !== true  ){
                $this->error = $error;
                return false;
            }

            //Check the validity of the elements
            $valid = true;
            foreach( $this->elements as $element_name => $element ){

                if ( in_array($element->getFullName(), $skip) )
                    continue;

                if( $element instanceof Helper_Form_Element_Valued )
                    if( !$element->isValid() ){
                        $valid = false;
                    }
            }

            return $valid;

        }

        /**
         * Returns a string with all errors
         *
         * @param  $separator  Separate the errors with this string
         * @return string      A string of errors separated by $separator
        */
        public function getError( $separator = " , " , $array = false ){

            $errors = array();

            if( !empty( $this->error ) ){
                $errors[] = $this->error;
            }else{

                foreach( $this->elements as $element ){

                    $element_name = $element->getFullName();
                    $suberrors    = array();
                    if( !($element instanceof Helper_Form_Element_Valued) )
                        continue;

                    if( $element instanceof Helper_Form_Element_Container ){
                        $suberror    = $element->getErrors( $separator );
                        $collect     = $element->getOption('collect_errors');

                        if( $collect )
                            $suberrors[ $element_name ] = implode( $separator , $suberror );
                        else
                            $suberrors = $suberror;

                    }else{
                        $suberror = $element->getError( $separator );
                        $suberrors[ $element_name] = $suberror;
                    }

                    foreach( $suberrors as $subname => $suberror ){
                        if( $suberror )
                            $errors[ $subname ] = $suberror;
                    }

                }

            }

            if( $array ){
                return $errors;
            }elseif( empty( $errors ) ){
                return null;
            }else{
                return implode( $separator , $errors );
            }

        }

        /**
         * Get the errors for all elements
         *
         * @return array          an array with all the errors indexed by the element name
        */
        public function getErrors( $separator = " , " ){
            return $this->getError( $separator , true );
        }


        public function hasError( ){

            //Check if parent is collecting our errors
            $parents = $this->getParents();
            foreach( $parents as $parent ){
                if( $parent->getOption('collect_errors') ){
                    return false;
                }
            }

            //If we are not collecting errors, we can't have an error
            $collect_errors = $this->getOption('collect_errors');
            if( !$collect_errors ){
                return false;
            }

            //Check if we have an actual error
            $error = $this->getError();
            return !empty( $error );

        }

        /**
         * Alias for hasError
         *
         * @return boolean
        */
        public function hasErrors( ){
            return $this->hasError( );
        }

        /**
         * Clear errors
         *
         * @return void
        */
        public function resetError( ){
            $this->error = null;
            foreach( $this->element as $element_name => $element ){
                if( $element instanceof Helper_Form_Element_Valued )
                    $element->resetError();
            }
        }

        /**
         * Alias for clearError
         * @return void
        */
        public function resetErrors( ){
            return $this->resetError();
        }

        /**
         * Returns a string with all errors
         *
         * @param  $separator  Separate the errors with this string
         * @return string      A string of errors separated by $separator
        */
        public function getWarning( $separator = " , " ){

            $collect_errors = $this->getOption('collect_errors');
            $errors = array();

            if( !empty( $this->warning ) ){
                $errors[] = $this->warning;
                return $errors;
            }

            if( !$collect_errors )
                return false;

            foreach( $this->elements as $element_name => $element ){
                if( $element instanceof Helper_Form_Element_Valued )
                    if( $element->hasWarning( ) ){
                        $errors[ $element_name ] = $element->getWarning();
                    }
            }

            if( empty( $errors ) )
                return null;
            elseif( $separator )
                return implode( $separator , $errors );
            else
                return $errors;

        }

        /**
         * Get the errors for all elements
         *
         * @return array          an array with all the errors indexed by the element name
        */
        public function getWarnings( $separator = " , "  ){
            return $this->getWarning( $separator );
        }

        /**
         * Alias for hasWarning
         *
         * @return boolean
        */
        public function hasWarnings( ){
            return $this->hasWarning();
        }

        /**
         * Alias for clearWarnings
         * @return void
        */
        public function resetWarning( ){
            $this->warning = null;
            foreach( $this->element as $element_name => $element )
                if( $element instanceof Helper_Form_Element_Valued )
                    $element->resetWarning();
        }

        /**
         * Alias for clearWarning
         *
         * @return void
        */
        public function resetWarnings( ){
            $this->resetWarning();
        }

       /**
        * Countable Interface count
        */
        public function count(){
            return count( $this->elements );
        }

       /**
        * Iterator's Interface rewind
        */
        public function rewind() {
            reset($this->elements);
        }

       /**
        * Iterator's Interface current
        */
        public function current() {
            return current($this->elements);
        }

       /**
        * Iterator's Interface key
        */
        public function key() {
            return key($this->elements);
        }

       /**
        * Iterator's Interface next
        */
        public function next() {
            return next( $this->elements );
        }

       /**
        * Iterator's Interface valid
        */
        public function valid() {
            return key($this->elements) !== null;
        }

       /**
        * Array interface offsetExists
        *
        * @return boolean
        */
        public function offsetExists( $key ){
            return $this->hasElement( $key );
        }

       /**
        * Array interface offsetGet
        *
        * @return mixed
        */
        public function offsetGet( $key ){
            if( $this->hasElement( $key ) ){
                return $this->getElement( $key );
            }
            return null;
        }

       /**
        * Array interface offsetSet
        *
        * @return void
        */
        public function offsetSet( $key , $value ){
            if( $this->hasElement( $key ) ){
                $this->getElement( $key )->setValue( $value );
            }
        }

       /**
        * Array interface offsetUnset
        *
        * @return void
        */
        public function offsetUnset( $key ){
            $this->removeElement( $key );
        }

       /**
        * Render
        *
        * @return void
        */
        public function render( $options = array() ){

            $Html = new Html();
            $attributes            = $this->getAttributes( true );
            $attributes['id']      = $this->getDomId();
            $attributes['class'][] = 'ym-form-container';

            list( $type , $options ) = $this->getRendererOptions( 'Layout' , array() );
            $content = Helper_Form_Renderer_Layout::factory( $type )->render( $this , $options );

            $Html->open( 'div' , $attributes );
            $Html->text( $content );
            $Html->close('div');

            return $Html->get();

        }

        public function getTranslationStrings()
        {

            $strings   = parent::getTranslationStrings();
            foreach( $this->elements as $element ){
                $sub_strings = $element->getTranslationStrings();
                $strings     = array_merge( $strings , $sub_strings );
            }

            return $strings;

        }

    }
