<?php

    abstract class Helper_Form_Element_Valued extends Helper_Form_Element{

        protected $error                  = null;
        protected $warning                = null;
        protected $validations            = array();
        protected $warnings               = array();
        protected $filters                = array();
        protected $value                  = null;
        protected $passed_value           = null;
        protected $has_passed_value       = false;
        protected $observing_form_changes = false;
        private   $cache                  = array();

        public function __clone(){
            $this->cache = array();

            parent::__clone();
        }


        public function setupOptions(){
            parent::setupOptions();
            $this->addOption( "domname"     , null );
            $this->addOption( "required"    , null );
            $this->addOption( "validations" , array() );
            $this->addOption( "warnings"    , array() );
            $this->addOption( "filters"     , array() );
            $this->addOption( "default"     , null   );
            $this->addOption( "sanitize"    , 'text' );
            $this->addOption( "show_error"  , true );
            $this->addOption( "error_label" , null );
        }

        /**
          * Add a new filter
          *
         * @param  string | Filter              the name of the filter to add or a filter object
         * @param  array $validation_options    the options for the filter
         * @return Filter
        */
        public function addFilter( $filter_name , $filter_options = array() ){

            //Clear Cache
            unset( $this->cache['getValue'] );

            $filter = null;

            //If a filter object was passed we just save it
            if( $filter_name instanceof Filter ){

                $filter = $filter_name;

            //If a string was passed get the class
            }elseif( is_string( $filter_name ) ){

                //Load the validation
                $class = Inflector::classify( $filter_name );
                $class = "Filter_".$class;
                if( !class_exists( $class ) ){
                    throw new Exception( "Coundn't load class $class" );
                }
                $filter = new $class( $filter_options );

            }

            //Save the filter
            $this->filters[] = $filter;

            //Return it
            return $filter;

        }

        /**
          * Add multiple filters at once
          *
          * @return array
        */
        public function addFilters( $filters ){

            //Delete previous filters
            $this->filters = array();

            if( is_string( $filters ) ){
                $filters = array( $filters );
            }

            foreach( $filters as $filter_name => $filter_options ){
                if( $filter_options instanceof Filter ){
                    $this->addFilter( $filter_options );
                }elseif( is_numeric( $filter_name ) && is_string( $filter_options ) ){
                    $this->addFilter( $filter_options , array() );
                }else{
                    $this->addFilter( $filter_name , $filter_options );
                }
            }

            return $this->filters;

        }

        /**
          * Add a new validation
          *
         * @param  string | Validation              the name of the validation to add or a validation object
         * @param  array $validation_options        the options for the validation
         * @param  boolean $warning                 is the validation a warning
         * @return Validation
        */
        public function addValidation( $validation_name , $validation_options = array() , $warning = false ){

            $validation = null;

            //If a validation object was passed we just save it
            if( $validation_name instanceof Validation ){

                $validation = $validation_name;

            //If a string was passed get the class
            }elseif( is_string( $validation_name ) ){

                //Load the validation
                $class = Inflector::classify( $validation_name );
                $class = "Validation_".$class;
                if( !class_exists( $class ) ){
                    throw new Exception( "Coundn't load class $class" );
                }

                $validation = new $class( $validation_options );

            }

            //Save the validation
            if( $warning ){
                $this->warnings[]    = $validation;
            }else{
                $this->validations[] = $validation;
            }

            //Return it
            return $validation;

        }

        /**
          * Add multiple validations at once
          *
          * @return array
        */
        public function addValidations( $validations = array() , $warning = false ){

            //Delete previous validations
            if( $warning ){
                $this->warnings    = array();
            }else{
                $this->validations = array();
            }


            if( is_string( $validations ) ){
                $validations = array( $validations );
            }

            foreach( $validations as $validation_name => $validation_options ){

                if( $validation_options instanceof Validation ){
                    $this->addValidation( $validation_options , null , $warning );
                }elseif( is_numeric( $validation_name ) && is_string( $validation_options ) ){
                    $this->addValidation( $validation_options , array() , $warning );
                }else{
                    $this->addValidation( $validation_name , $validation_options , $warning );
                }
            }

            return $this->validations;

        }

        /**
          * Add a new warning
          *
          * alias of addValidation( $validation_name , $validation_options , true );
          * @return Validation
        */
        public function addWarning( $validation_name , $validation_options = array() ){
            return $this->addValidation( $validation_name , $validation_options , true );
        }

        /**
          * Add multiple warnings at once
          *
          * @return array
        */
        public function addWarnings( $validations ){
            return $this->addValidations( $validations , true );
        }

        /**
         * getDefaultValue
         *
         * @return mixed
        */
        public function getDefaultValue(){
            $value = $this->getOption('default');
            return $this->normalizeValue( $value );
        }

        /**
         * getDomName
         *
         * @return string
        */
        public function getDomName(){

            //If specified return it
            $domname = $this->getDomNamePath();
            $parent_names = explode( "." , $domname );
            $name         = array_pop( $parent_names );
            $super_parent = array_shift( $parent_names );

            $full_name    = array();

            if( $super_parent ){
                $full_name[]  = $super_parent;
            }

            foreach( $parent_names as $parent_name ){
                $full_name[] = '['.$parent_name.']';
            }

            if( empty( $full_name ) ){
                $full_name[] = $name;
            }else{
                $full_name[] = '['.$name.']';
            }

            $full_name = implode( "" , $full_name );
            return $full_name;
        }

        /**
         * getDomNamePath
         *
         * @return string
        */
        public function getDomNamePath(){

            $domname = $this->getOption("domname");
            if( $domname ) return $domname;
            else           return $this->getFullName();
        }

        /**
         * Returns a string with the error of this element
         *
         * @return string      A string of representing the error
        */
        public function getError( ){
            return $this->error;
        }

        /**
         * Returns a string with label for the error of this element
         *
         * @return string      A string of representing the error label
        */
        public function getErrorLabel(){

            if ($this->getOption('error_label')) {
                return $this->getOption('error_label');
            }

            return $this->getLabel();
        }

        /**
          * Get all filters
          *
          * @return array
        */
        public function getFilters(){
            $parent  = $this->getParent();
            $parents = array();
            if( $parent ){
                $parents = $parent->getFilters();
            }
            return array_merge( $parents , $this->filters );
        }

        /**
         * getSubmissionValue
         *
         * @return mixed
        */
        public function getSubmissionValue(  ){

            if( array_key_exists( __FUNCTION__ , $this->cache ) ) {
                return $this->cache[ __FUNCTION__ ];
            }

            //Get the fullname
            $form         = $this->getForm();
            $domname      = $this->getDomNamePath();

            //Get the submission value
            $sanitize = $this->getOption('sanitize');
            $method   = $form && $form->getOption('method') == 'POST' ? $_POST : $_GET;
            $value    = input( $method , $domname , null , $sanitize );
            $value    = $this->normalizeValue( $value );

            //Return
            return $this->cache[ __FUNCTION__ ] = $value;

        }

        /**
          * Get the unfiltered value of the element
          *
          * @return mixed                             the value of the element
        */
        public function getUnfilteredValue(  ){

            if( array_key_exists( __FUNCTION__ , $this->cache ) )
                return $this->cache[ __FUNCTION__ ];

            //Get the value
            $posted = false;
            if( $this->has_passed_value ){
                $value = $this->passed_value;
            }else{
                $value  = $this->getSubmissionValue();
                $posted = ($value !== null);
            }

            //If value is empty set default
            $present  = $this->isPresent( $value );
            $required = $this->isRequired();

            if( !$present && $posted && $required ){
                $value = null;
            }elseif( !$present ){
                $value = $this->getDefaultValue();
            }

            return $this->cache[ __FUNCTION__ ] = $value;

        }

        /**
          * Get all validations
          *
          * @return array
        */
        public function getValidations( $warnings = false ){
            $parent  = $this->getParent();
            $parents = array();
            $validations = $warnings ? $this->warnings : $this->validations;
            if( $parent ){
                $parents = $parent->getValidations( $warnings );
            }

            $validations = array_merge( $parents , $validations );
            return $validations;
        }

        /**
          * Get the value of the element
          *
          * @param mixed $default                     the default value
          * @return mixed                             the value of the element
        */
        public function getValue( ){

            if( array_key_exists( __FUNCTION__ , $this->cache ) ){
                return $this->cache[ __FUNCTION__ ];
            }

            //Get the value
            if( !$this->areDependsSatisfied() ){
                $value = null;
            }else{
                $value = $this->getUnfilteredValue();
            }

            //Filter the value
            $filters = $this->getFilters();
            foreach( $filters as $filter ){
                $value = $filter->filter( $value );
            }

            //Return
            return $this->cache[ __FUNCTION__ ] = $value;

        }

        /**
         * Returns a string with the warning of this element
         *
         * @return string      A string of representing the error
        */
        public function getWarning( ){
            return $this->warning;
        }

        /**
          * Alias for getValidations( true )
          *
          * @return array
        */
        public function getWarningValidations( ){
            return $this->getValidations( true );
        }

        /**
         * Check if the element has an error
         *
         * @return boolean
        */
        public function hasError( ){

            //Check if parent is collecting our errors
            $parents = $this->getParents();
            foreach( $parents as $parent ){
                if( $parent->getOption('collect_errors') )
                    return false;
            }

            //Check if we have an actual error
            $error = $this->getError();
            return !empty( $error );
        }

        /**
         * Check if the element has a warning
         *
         * @return boolean
        */
        public function hasWarning( ){
            $error = $this->getWarning();
            return !empty( $error );
        }

        /**
         * Checks if this element can be considered present
         * this is meant to be redifined in sub classes
         *
         * @param           value
         * @return bool     if the element should be considered present
        */
        protected function isPresent( $value ){

            if( $value === null )
                return false;

            if( is_array( $value ) && !count( $value ) )
                return false;

            if( is_bool( $value ) )
                return true;

            if( is_scalar( $value ) && trim( (string)$value ) == '' )
                return false;

            return true;
        }

        /**
         * Checks if this element is required
         * alias for getOption('required')
         *
         * @return boolean       is the element required
        */
        public function isRequired( ){
            return (bool)$this->getOption('required' , false );
        }

        public function getRequiredMessage( ){
            $required = $this->getOption('required' , false );
            if( is_string( $required ) )
                return t($required);
            else
                return t("%{label} is required");
        }

        /**
         * Checks if this element is valid
         * after this method is called errors and warnings will be available
         *
         * @return boolean
        */
        public function isValid(){

            //Check if the element already has been invalidated
            if( $this->error !== null )
                return false;

            //Dont Validate if the dependecies are not satisifed
            if( !$this->areDependsSatisfied() ){
                return true;
            }

            //Get the value
            $value    = $this->getValue();
            $required = $this->isRequired();
            $present  = $this->isPresent( $value );

            //Check if its required and not
            //present stop validating
            if( !$required && !$present ){
                return true;
            }

            //Check if its required
            if( $required && !$present ){

              $message  = $this->getRequiredMessage();
              $options = $this->getOptions();
              $options['label'] = $this->getErrorLabel();

              $template    = new Template( $message );
              $this->error = $template->apply( $options );
              return false;
            }

            //Check built in validation
            $error = $this->validate();
            if( $error !== true  ){
                $this->error = $error;
                return false;
            }


            //Check the validations
            foreach( $this->getValidations() as $validation ){

                if( !$validation->validate( $value , $this->getParent() ) ){
                    $options = array();
                    $options['label'] = $this->getErrorLabel();
                    $this->error = $validation->getMessage( $options );
                    return false;
                }

            }

            //Check the warnings
            foreach( $this->getWarningValidations() as $validation ){
                if( !$validation->validate( $value , $this->getParent() ) ){
                    $options = array();
                    $options['label'] = $this->getErrorLabel();
                    $this->warning = $validation->getMessage( $options );
                    break;
                }
            }

            //If we got here then we are valid
            return true;

        }

        /**
         * Reset the element
         *
         * this removes any errors and restores the element
         * as if no value was passed or posted
         *
         * @return void
        */
        public function reset(){
            $this->resetValue();
            $this->resetError();
            $this->resetWarning();
        }

        /**
         * Reset error
         *
         * @return void
        */
        public function resetError( ){
            $this->error = null;
        }

        /**
         * Reset warnings
         *
         * @return void
        */
        public function resetWarning( ){
            $this->warning = null;
        }

        /**
         * Reset the value of the element
         *
         * this sets the value of the element to its default
         *
         * @return void
        */
        public function resetValue(){

            //Remove passed value
            $this->passed_value     = null;
            $this->has_passed_value = false;

            //Remove value caches
            unset( $this->cache['getValue'] );
            unset( $this->cache['getUnfilteredValue'] );

            //Make sure submission value returns null
            $this->cache['getSubmissionValue'] = null;

            //Notify change
            $event = new Event( $this , "form.value.changed" );
            Event::notify( $event );

        }

        /**
          * Set the default value
          *
          * @param mixed $value                       the value to set
          * @return mixed                             the value to set
        */
        public function setDefaultValue( $default = null ){

            //Remove Cache
            unset( $this->cache['getValue'] );
            unset( $this->cache['getUnfilteredValue'] );

            //Set the default value
            $this->setOption( 'default' , $default );

            //Notify change
            $event = new Event( $this , "form.value.changed" );
            Event::notify( $event );

        }

        /**
         * Force an error on this element
         *
         * @param  string $error  An string representing the error
        */
        public function setError( $error ){
            $this->error = $error;
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

            if( isset( $options['validations'] ) ){
                $this->addValidations( $options['validations'] );
            }

            if( isset( $options['warnings'] ) ){
                $this->addWarnings( $options['warnings'] );
            }

            if( isset( $options['filters'] ) ){
                $this->addFilters( $options['filters'] );
            }

            return $options;

        }

        /**
         * Set if the element is required
         *
         * @return boolean       is the element required
        */
        public function setRequired( $message ){
            return $this->setOption('required' , $message );
        }

        /**
          * Set the value of the element
          *
          * @param mixed $value                       the value to set
          * @return mixed                             the value to set
        */
        public function setValue( $value  ){

//if (strpos($this->getDomId(), 'form_constraints') !== false) pr($this->getDomId(), $value);

            //Remove Cache
            unset( $this->cache['getValue'] );
            unset( $this->cache['getUnfilteredValue'] );

            //Set the value
            $this->passed_value    = $this->normalizeValue( $value );
            $this->has_passed_value = true;

            //Notify change
            $event = new Event( $this , "form.value.changed" );
            Event::notify( $event );

        }

        /**
         * Force a warning on this element
         *
         * @param  string $error       An string representing the warning
        */
        public function setWarning( $error ){
            $this->warning = $error;
        }

        /**
         * If implemented on a sub class will return of the value is valid
         *
         * @param  string $error  An string representing the error
        */
        protected function validate(){
            return true;
        }

        protected function normalizeValue( $value ){
            return $value;
        }

        public function getLabel()
        {
            $label = $this->getOption('label');

            if( $label === null ){
                $name  = $this->getName();
                $label = Inflector::humanize( $name );
            }

            return t($label);
        }

        public function getTranslationStrings()
        {

            $strings   = parent::getTranslationStrings();

            $errorLabel = $this->getOption('error_label');
            if( $errorLabel ) $strings[] = $errorLabel;

            //Translate Required Message
            $required = $this->getOption('required' , false );
            if( is_string($required) ) $strings[] = $required;

            //Translate Validations
            $validations = $this->getValidations();
            foreach( $validations as $validation ){
                $sub_strings = $validation->getTranslationStrings();
                $strings     = array_merge( $strings , $sub_strings );
            }

            return $strings;

        }

    }


