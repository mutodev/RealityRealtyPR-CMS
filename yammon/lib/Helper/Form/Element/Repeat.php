<?php

    class Helper_Form_Element_Repeat extends Helper_Form_Element_SubForm{

        private $template = null;

        public function __construct( $name , $options = array() , $parent = null ){
            parent::__construct( $name , $options , $parent );
            $this->template();
        }

        public function setupOptions(){
            parent::setupOptions();
            $this->addOption( "min_repeats"          , 1 );
            $this->addOption( "max_repeats"          , null );
            $this->addOption( "start_repeats"        , 2 );
            $this->addOption( "sortable"             , false );
            $this->addOption( "sortable_column"      , 'position' );
            $this->addOption( "sortable_column_sort" , false );

            $this->setOption( "collect_errors"     , false );
            $this->setOption( 'layout_renderer'   , 'horizontal' );
            $this->setOption( 'box_renderer'      , array(
                'type'      => '1Column' ,
                'margin'    => true ,
                'padding'   => true ,
                'border'    => true ,
                'highlight' => true ,
            ));
            $this->setOption( 'default_renderers' , array(
                'label_renderer' => array(
                    'type' => 'inline' ,
                    'small' => true ,
                ),
                'description_renderer' => array(
                    'type' => 'inline' ,
                ),
                'box_renderer' => array(
                    'type'    => '1Column' ,
                    'border'  => false ,
                    'padding' => false ,
                    'margin'  => false  ,
                )
            ));

        }

        public function build(){
            parent::build();

            //Add javascript
            $Javascript = helper('Javascript');
            $Javascript->add("/yammon/public/widget/widget.js");
            $Javascript->add("/yammon/public/widget/widget-repeat.js");

            //Create Elements
            $values = $this->getSubmissionValue();
            unset( $values['__template__'] );

            $this->createElements( $values );
        }

        protected function template(){
            if( !$this->template ){
                $options         = $this->getOptions();
                $options['type'] = 'SubForm';
                $options['name'] = '__template__';
                $this->template = parent::addElement( $options );
            }
            return $this->template;
        }

        protected function element( $i , $create = true )
        {
            $name = "__{$i}__";

            if( isset( $this->elements[ $name ] ) )
                return $this->elements[ $name ];

            if( !$create )
                return null;

            $template = $this->template();
            $element = clone( $this->template );
            $element->setName( $name );
            $element = parent::addElement( $element );

            return $element;
        }

        public function addElement( $element ){

            if( is_object( $element ) )
                $element = clone( $element );

            $template = $this->template();
            $return   = $template->addElement( $element );
            foreach( $this->elements as $current ){
                if( $current != $template )
                    $current->addElement( $element );
            }

            return $return;

        }

        public function removeElement( $element ){

            if( is_object( $element ) )
                $element = clone( $element );

            $template = $this->template();
            $return   = $template->removeElement( $element );
            foreach( $this->elements as $current ){
                if( $current !== $template )
                    $current->removeElement( $element );
            }

            return $return;

        }

        public function removeElements(){
            foreach( $this->elements as $current ){
                $current->removeElements( );
            }
        }

        public function getEntry( $i , $create = false ){
            return $this->element( $i , $create );
        }

        public function reset(){

            $min_repeats   = $this->getOption("min_repeats");
            $start_repeats = $this->getOption("start_repeats");

            $repeats  = $start_repeats > $min_repeats ? $start_repeats : $min_repeats;
            $template = $this->template();

            $i = 0;
            foreach( $this->elements as $current ){
                if( $current !== $template ) {

                    if ($i > $repeats-1) {
                        $name = $current->getName();
                        parent::removeElement( $name );
                    }

                    $i++;
                }
            }

            parent::reset();
        }

        protected function createElements( $values = array() ){

            $min_repeats   = $this->getOption("min_repeats");
            $max_repeats   = $this->getOption("max_repeats");
            $start_repeats = $this->getOption("start_repeats");

            $repeats       = count( $values );

            if( $repeats == 0 && $start_repeats ){
                $repeats = $start_repeats;
            }

            if( $min_repeats && $repeats < $min_repeats ){
                $repeats = $min_repeats;
            }

            if( $max_repeats && $repeats > $max_repeats  ){
                $repeats = $max_repeats;
            }

            if( $repeats <= 0 ){
                $repeats = 1;
            }

            for( $i = 0 ; $i < $repeats ; $i++ ){
                $this->element( $i );
            }
        }

        public function setValue( $element_or_values , $element_value = null )
        {
            $autoSort = $this->getOption('sortable_column_sort');
            $values   = $this->normalizeValue( $element_or_values , $element_value );

            if ( $autoSort )
                usort($values, array($this, 'sortableColumnSort'));

            $this->createElements( $values );

            //Transform Element Names
            $names = array();
            foreach( $values as $key => $value )
                $names[] = preg_match( "/^__[^_]+__$/", $key ) ? $key : "__{$key}__";

            if( count( $values ) )
                $values = array_combine($names, $values);

            return parent::setValue( $values );
        }

        public function getValue( )
        {
            $newValues = array();
            $value     = parent::getValue();

            unset( $value['__template__'] );

            foreach( $value as $k => $v ){
                $isPresent = $this->elements[$k]->isPresent( $v );
                if( $isPresent ){
                    $v['position'] = count( $newValues );
                    $newValues[]  = $v;
                }
            }

            return $newValues;
        }

        public function getUnfilteredValue( ){
            $value = parent::getUnfilteredValue( );
            unset( $value['__template__'] );
            return $value;
        }

        public function isValid(){
            $skip = $this->getFullName() . '.__template__';
            return parent::isValid( $skip );
        }

        public function renderBox(){
            return $this->render();
        }

        public function render( $options = array() ){

            //Create Elements
            $this->createElements();

            $Html = new Html();

            //Get the container attributes
            $attributes            = $this->getAttributes( true );
            $attributes['id']      = $this->getDomId();
            $attributes['class'][] = 'ym-form-container';
            $attributes['class'][] = 'ym-form-repeat';
            $attributes['widget']  = 'Repeat';
            $attributes['widget-repeat-min'] = $this->getOption('min_repeats');
            $attributes['widget-repeat-max'] = $this->getOption('max_repeats');
            $attributes['widget-repeat-sortable'] = $this->getOption('sortable');

            //Start the Repeat
            $Html->open( 'div' , $attributes );

                //Render the template
                $elements = $this->elements;
                $template = $elements['__template__'];
                $Html->open( 'div' , array( 'class' => 'ym-form-repeat-template' , 'style' => 'display:none' ) );
                    $Html->text( $template->renderBox() );
                $Html->close('div');
                unset( $elements['__template__'] );

                //Display the elements
                $odd = false;
                $i   = 0;
                foreach( $elements as $element ){
                    $classes   = array();
                    $classes[] = "ym-form-repeat-item";
                    $classes[] = $odd ? "ym-form-repeat-item-odd" : "ym-form-repeat-item-even";
                    $Html->open( 'div' , array('class' => $classes, 'data-index' => $i ) );
                        $Html->text( $element->renderBox() );
                    $Html->close('div');
                    $odd = !$odd;

                    $i++;
                }


            //End Repeat
            $Html->close('div');

            return $Html->get();

        }

        protected function sortableColumnSort($a, $b)
        {
            $column = $this->getOption('sortable_column');

            if (@$a[$column] == @$b[$column])
                return 0;

            return (@$a[$column] < @$b[$column]) ? -1 : 1;
        }
    }

