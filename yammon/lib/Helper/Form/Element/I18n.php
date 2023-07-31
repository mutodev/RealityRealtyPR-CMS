<?php

    class Helper_Form_Element_I18n extends Helper_Form_Element_Container
    {
        private $template = null;

        public function __construct( $name , $options = array() , $parent = null )
        {
            parent::__construct( $name , $options , $parent );
            $this->template();
        }

        public function setupOptions()
        {
            parent::setupOptions();

            $this->addOption( "default_language", 'en' );
            $this->addOption( "languages", array(
                'en' => array(
                    'label'    => 'English',
                    'required' => true,
                ),
                'es' => array(
                    'label'    => 'Spanish',
                    'required' => true,
                )
            ));

            $this->setOption( "collect_errors"  , false );

            $this->setOption( 'box_renderer'    , array(
                'type'      => '1Column' ,
                'margin'    => true  ,
                'padding'   => '5px 0 0 0' ,
                'border'    => true  ,
                'highlight' => true
            ));

            $this->setOption( 'default_renderers' , array(
                'box_renderer' => array(
                    'type'    => '2Column' ,
                    'border'  => false ,
                    'padding' => true ,
                    'margin'  => '0 0 5px 0'  ,
                )
            ));
        }

        public function build()
        {
            parent::build();

            //Add javascript
            $Javascript = helper('Javascript');
            $Javascript->add("/yammon/public/widget/widget.js");
            $Javascript->add("/yammon/public/widget/widget-i18n.js");

            //Add CSS
            $Css = helper('Css');
            $Css->add("/yammon/public/form/css/i18n.css");

            //Create Elements
            $this->createElements();
        }

        protected function template()
        {
            if( !$this->template ){
                $options['type'] = 'SubForm';
                $options['name'] = '__template__';
                $this->template = parent::addElement( $options );
            }
            return $this->template;
        }

        protected function createElements()
        {
            $languages = $this->getOption( "languages" );

            foreach( $languages as $language => $options ){
                $this->element( $language );
            }
        }

        protected function element( $language , $create = true )
        {
            if( isset( $this->elements[ $language ] ) )
                return $this->elements[ $language ];

            if( !$create )
                return null;

            $template = $this->template();
            $element = clone( $this->template );
            $element->setName( $language );
            $element->setOptions( $this->getOptions() );
            $element = parent::addElement( $element );

            foreach ( $element->getElements() as $subElement )
                $this->elementSettings( $language, $subElement );

            return $element;
        }

        public function addElement( $element )
        {
            if( is_object( $element ) )
                $element = clone( $element );

            $template = $this->template();
            $return   = $template->addElement( $element );
            foreach( $this->elements as $language => $current ){

                if( $current != $template ) {

                    $LanguageElement = $current->addElement( $element );

                    $this->elementSettings( $language, $LanguageElement );
                }
            }

            return $return;
        }

        protected function elementSettings( $language, $Element )
        {
            $languages       = $this->getOption( "languages" );
            $languageSetting = $languages[$language];

            //Not Required
            if ( !isset( $languageSetting['required'] ) || !$languageSetting['required'] ) {
                $Element->setOption('required', false);

                if ( $Element instanceof Helper_Form_Element_Container ) {
                    foreach( $Element->getElements() as $SubElement )
                        $this->elementSettings( $language, $SubElement );
                }
            }

        }

        public function setValue( $element_or_values , $element_value = null )
        {
            $languages = $this->getOption( "languages" );
            $values    = $this->normalizeValue( $element_or_values , $element_value );

            $languageValues = array();
            foreach( $this->template()->getElements() as $name => $element ) {

                foreach( $languages as $language => $options ){
                    $key = "{$name}_$language";

                    $languageValues[ $language ][ $name ] = @$values[ $key ];
                }
            }

            $this->createElements( );

            return parent::setValue( $languageValues );
        }

        public function getValue()
        {
            $newValues       = array();
            $languagesValues = parent::getValue();

            unset( $languagesValues['__template__'] );

            foreach( $languagesValues as $language => $values ){
                $isPresent = $this->elements[$language]->isPresent( $values );
                if( $isPresent ){

                    foreach( $values as $name => $value ){
                        $key = "{$name}_$language";

                        $newValues[$key] = $value;
                    }

                    $newValues[$language] = $values;
                }
            }

            return $newValues;
        }

        public function getUnfilteredValue()
        {
            $value = parent::getUnfilteredValue();
            unset( $value['__template__'] );
            return $value;
        }

        public function getErrors( $separator = " , " )
        {
            $languages = $this->getOption( "languages" );
            $errors    = $this->getError( $separator , true );

            if ( !is_array($errors) )
                return $errors;

            foreach( $errors as $key => &$error ) {
                preg_match('/'.$this->getFullName().'.([^\.]+)/i', $key, $matches);

                if ( isset($matches[1]) ) {
                    $language = $matches[1];
                    $error    = "[{$languages[$language]['label']}] {$error}";
                }
            }

            return $errors;
        }

        protected function getErrorsLanguage()
        {
            $errors    = $this->getError( '' , true );
            $languages = array();

            if ( !is_array($errors) )
                return array();

            foreach( $errors as $key => &$error ) {
                preg_match('/'.$this->getFullName().'.([^\.]+)/i', $key, $matches);

                if ( isset($matches[1]) )
                    $languages[] = $matches[1];
            }

            return $languages;
        }

        public function isValid()
        {
            return parent::isValid( $this->elements['__template__']->getFullName() );
        }

        public function renderBox( $options = array() )
        {
            return $this->render( $options );
        }

        public function render()
        {
            //Create Elements
            $this->createElements();

            //Options
            $languages       = $this->getOption( "languages" );
            $defaultLanguage = $this->getOption( "default_language" );

            //Error Languages
            $errorLanguages  = $this->getErrorsLanguage();
            $defaultLanguage = count($errorLanguages) ? array_shift($errorLanguages) : $defaultLanguage;

            $elements = $this->elements;
            unset($elements['__template__']);

            $Html = new Html();

            //Get the container attributes
            $attributes            = $this->getAttributes( true );
            $attributes['id']      = $this->getDomId();
            $attributes['class'][] = 'ym-form-container';
            $attributes['class'][] = 'ym-form-i18n';
            $attributes['widget']  = 'i18n';

            $Html->open( 'div' , $attributes );

                $Html->text('<ul class="ym-form-i18n-language-selector">');

                $activeClass = "ym-form-i18n-language-selector-active";
                foreach( $languages as $language => $options) {

                    $classes   = array();
                    $classes[] = 'ym-form-box-border';
                    $classes[] = ( $defaultLanguage == $language ) ? $activeClass : '';

                    $Html->text('<li class="'.implode(' ', $classes).'">');
                        $Html->text('<a href="#'.$options['label'].'" lang="'.$language.'">'.t($options['label']).'</a>');
                    $Html->text('</li>');
                }

                $Html->text('</ul>');

                //Display the elements
                $inactiveClass = "ym-form-i18n-item-hide";
                foreach( $elements as $language => $element ){
                    $classes   = array();
                    $classes[] = ( $defaultLanguage != $language ) ? $inactiveClass : '';
                    $classes[] = "clear";
                    $classes[] = "ym-form-i18n-item";
                    $classes[] = "ym-form-i18n-item-lang-$language";

                    $Html->open( 'div' , array('class' => $classes ) );
                        $Html->text( $element->renderBox() );
                    $Html->close('div');
                }

            $Html->close('div');

            return $Html->get();
        }
    }
