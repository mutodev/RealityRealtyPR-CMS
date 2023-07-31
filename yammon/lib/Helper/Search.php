<?php

    class Helper_Search extends Helper{

        protected $search;
        protected $form_simple;
        protected $form_fields;

        public function __construct( $name , $options = array() ){
            $this->search = new Search();
            parent::__construct( $name , $options );

           $Css        = helper("Css");
           $Css->add('/yammon/public/search/css/search.css');

        }

        public function setupOptions(){

           parent::setupOptions();

           $this->addOptions( array(
                "label"      => null  ,
                "argument"   => "q" ,
                "columns"    => 4   ,
                "parameters" => array() ,
                "advanced"   => true ,
                "default"    => "simple" ,
           ));

        }

        function setOptions( $options ){

            //Initialize the search
            $options = parent::setOptions( $options );
			$this->search->setOptions( $options);
			return $options;
        }

        public function getElement( $element ){
            $argument = $this->getOption("argument");
            return $this->getFieldsForm()->getElement( $argument.".".$element );
        }

        public function removeElement( $element ){
            $argument = $this->getOption("argument");
            return $this->getFieldsForm()->removeElement( $argument.".".$element );
        }

        public function getArgument(){
            return $this->getOption('argument');
        }

        private function createField( $parent , $field ){

            $argument         = $this->getOption("argument");
            $class            = get_class($field->getType());
            $element_options  = $field->getOption('element' , array() );

            //Get element type
            if( !isset( $element_options['type'] ) ){
                if( $class == 'Search_Type_Boolean' ){
                    $element_type = 'Select';
                }elseif( $class == 'Search_Type_Date' ){
                    $element_type = 'Date';
                    $element_options['labels'] = false;
                }else{
                    $element_type = 'Text';
                }
            }else{
                $element_type = $element_options['type'];
            }

            //Set default element options
            if( !isset( $element_options['size'] ) ){
                $element_options['size'] = 'full';
            }

            if( !isset( $element_options['label'] ) ){
                $element_options['label'] = $field->getLabel();
            }

            if( $class == 'Search_Type_Boolean' ){
                if( !isset( $element_options['options'] ) ){
                    $element_options['options'] = array( "1" => t("Yes") , "0" => t("No") );
                }
            }

            //Add element
            $name    = $field->getName();
            $element_options['type'] = $element_type;
            $element_options['name'] = $field->getName();
            $element = $parent->add( $element_options );
            return $element;

        }

        public function getFieldsForm(){

            if( !empty( $this->form_fields ) ){
                return $this->form_fields;
            }

            //Get Options
            $columns  = $this->getOption("columns"  , 3   );
            $argument = $this->getOption("argument");

            //Create Form
            $Form = new Helper_Form( $argument );
            $Form->setOption('method'    , 'GET' );

            //Pass the parameters thru the form
            $parameters             = (array)$this->getOption('parameters');
            $parameters_get         = $_GET;

            $parameters = array_merge( $parameters_get , $parameters  );
            unset( $parameters[ $argument ] );

            foreach( $parameters as $k => $v ){
                $Form->add( array(
                    'type' => 'hidden' ,
                    'name' => $k ,
                    'domname' => $k ,
                ))->setValue( $v );
            }

            //Add Field to form
            $fields = $this->search->getFields();

            if( !empty( $fields )){

                $Container = $Form->add( array(
                    "name"    => "advanced" ,
                    "type"    => 'container' ,
                    "label"   => "" ,
                    'layout_renderer' => array(
                        'type' => 'responsive' ,
                        'columns' => $this->getOption('columns') ,
                        'width'   => '100%' ,
                    ),
                    'box_renderer' => array(
                        'type' => '1Column' ,
                        'margin'    => false ,
                        'padding'   => false ,
                        'border'    => false ,
                    ),
                    'default_renderers' => array(
                        'label_renderer' => array(
                            'small' => true ,
                        ),
                        'box_renderer' => array(
                            'type'      => '1Column' ,
                            'margin'    => false ,
                            'padding'   => false ,
                            'border'    => false ,
                            'highlight' => false ,
                        )
                    ),
                ));

                foreach( $fields as $field ){
                    $advanced = $field->getOption('advanced' , true );
                    if( $advanced )
                        $this->createField( $Container , $field );
                }

            }

            $Form->add( array(
                "name"  => 'buttons' ,
                'type'  => 'buttons' ,
                "save"  => false ,
                "cancel" => false ,
                "buttons" => array(
                    "search" => array(
                      "label" => t('Search') ,
                      "class" => array('button', 'positive', 'btn', 'btn-success') ,
                      "value"    => false ,
                    ),
                    "clear" => array(
                      "label" => t('Clear') ,
                      "class" => array('button', 'negative', 'btn', 'btn-danger') ,
                      "href"  => url('.').qs( $argument , null , 's' , 'a' ) ,
                    )
                )
            ));

            //Set the values
//            $Form->reset();
//            $Form->setValues( $this->getSearch() );


            return $this->form_fields = $Form;

        }

        public function getSimpleForm(){

            if( !empty( $this->form_simple ) ){
                return $this->form_simple;
            }

            //Get Options
            $argument = $this->getOption("argument");
            $fields   = $this->search->getFields();

            $Form = new Helper_Form( $argument );
            $Form->setOption('method'    , 'GET' );


            //Pass the parameters thru the form
            $parameters             = (array)$this->getOption('parameters');
            $parameters_get         = $_GET;
            $parameters_get_exclude = Router::getNamedParameters();
            foreach( $parameters_get_exclude as $k => $v ){
                unset( $parameters_get[$k] );
            }
            $parameters = array_merge( $parameters_get , $parameters  );
            unset( $parameters[ $argument ] );

            foreach( $parameters as $k => $v ){
                $Form->add( array(
                    'type' => 'hidden' ,
                    'name' => $k ,
                    'domname' => $k ,
                ))->setValue( $v );
            }

            $Container = $Form->add( array(
                'name' => 'search' ,
                'type' => 'container' ,
                'layout_renderer' => array(
                    'type' => 'Horizontal' ,
                    'style' => 'width:100%' ,
                ),
                'box_renderer' => array(
                    'type' => '1Column' ,
                    'margin'    => false ,
                    'padding'   => false ,
                    'border'    => false ,
                ),
                'default_renderers' => array(
                    'box_renderer' => array(
                        'type'      => '1Column' ,
                        'margin'    => false ,
                        'padding'   => false ,
                        'border'    => false ,
                        'highlight' => false ,
                    )
                ),
                'label' => "" ,
            ));

                $el = $Container->add( array(
                    'name'     => '*' ,
                    'type'     => 'Text' ,
                    'label'    => '' ,
                    'style'    => 'width:100%' ,
                    'colwidth' => '99%' ,
                ));

                $Container->add( array(
                  "name"     => 'submit' ,
                  "type"     => 'button' ,
                  "label"    => t('Search') ,
                  "class"    => array('button', 'neutral') ,
                  "colwidth" => 'auto' ,
                  "value"    => false ,
                ));

            $Extra = $Form->add( array(
                "name"    => "simple" ,
                "type"    => 'container' ,
                "label"   => "" ,
                'layout_renderer' => array(
                    'type' => 'Grid' ,
                    'columns' => $this->getOption('columns') ,
                ),
                'box_renderer' => array(
                    'type' => '1Column' ,
                    'margin'    => false ,
                    'padding'   => false ,
                    'border'    => false ,
                ),
                'default_renderers' => array(
                    'label_renderer' => array(
                        'small' => true ,
                    ),
                    'box_renderer' => array(
                        'type'      => '1Column' ,
                        'margin'    => false ,
                        'padding'   => false ,
                        'border'    => false ,
                        'highlight' => false ,
                    )
                ),
            ));

            $c = 0;
            foreach( $fields as $field ){
                $simple = $field->getOption('simple' , false );
                if( !$simple )
                    continue;
                $this->createField( $Extra , $field );
                $c++;
            }

            //Set the values
//            $Form->reset();
//            $Form->setValues( $this->getSearch() );

            return $this->form_simple = $Form;

        }

        public function hasSearch(){
            return $this->getSearch() != '';
        }

        public function getSearch(){

            $argument = $this->getOption('argument');

			if( $this->isAdvanced() )
				$query = $this->getFieldsForm()->getValues();
			else
				$query = $this->getSimpleForm()->getValues();


            //Get the real search
            if( is_array( $query ) ){
				$return = $query;
				unset( $return['submit'] );
            }else{
                $return['*'] = (string)$query;
            }

            return $return;

        }

        public function getClearSearchUrl(){
            $argument = $this->getOption('argument');
            return url('.'.qs( array( $argument => '' ) ) );
        }

        function search( $select = null ){
            $query    = $this->getSearch();

            if( !$query )
                return $select;
            else
                return $this->search->search( $select  , $query );

        }

        function getDQL( ){
            $query = $this->getSearch();
            return $this->search->search( null , $query );
        }

        public function isAdvanced()
        {
            $can_advanced = $this->getOption("advanced");

            $is_advanced = false;
            if( $can_advanced ){
                if( get('s') == null ){
                    $default = $this->getOption('default');
                    if( $default == 'advanced' )
                       $is_advanced = true;
                }elseif( get('s') == 'a' ){
                    $is_advanced = true;
                }
            }

            return $is_advanced;
        }

		public function render( $mode = 'fields' ){

            $html = array();

            $label        = $this->getOption("label" , t('Search') );
            $can_advanced = $this->getOption("advanced");
            $is_advanced  = $this->isAdvanced();

            $html[] = "<div class='yammon-search'>";

                //Mobile controls
                $html[] = '<div class="padding yammon-search-modify-container">';
                    $html[] = '<button class="btn btn-success btn-block yammon-search-modify">';
                        $html[] = t('Modify Search');
                    $html[] = '</button>';
                $html[] = '</div>';

                //Create simple/advanced links
                $simple_href = qs( 's' , 's' , 'q' , null );
                $adv_href    = qs( 's' , 'a' , 'q' , null );

                /* Header ------------------------------- */
                $html[] = "<div class='yammon-search-header'>";

                    if( $can_advanced ){
                        $html[] = "<div class='yammon-search-switcher'>";
                            $html[] = "<a class='".( !$is_advanced ? 'active' : '' )."' href='$simple_href'>";
                                $html[] = t("simple search");
                            $html[] = "</a>";
                            $html[] = "&nbsp;";
                            $html[] = "<a  class='".( $is_advanced ? 'active' : '' )."' href='$adv_href'>";
                                $html[] = t("advanced search");
                            $html[] = "</a>";
                        $html[] = "</div>";
                    }

                    if( $label ){
                        $html[] = "<h1>$label</h1>";
                    }

                    $html[] = "<div style='clear:both'></div>";

                $html[] = "</div>";

                if( !$is_advanced ){
                    /* Simple ------------------------------- */
                    $html[] = "<div class='yammon-search-simple'>";
                        $html[] = $this->getSimpleForm()->render();
                    $html[] = "</div>";
                }else{
                    /* Advanced ------------------------------ */
                    $html[] ="<div class='yammon-search-advanced'>";
                        $html[] = $this->getFieldsForm()->render();
                    $html[] = "</div>";
                }

            $html[] = "</div>";

            return implode("\n" , $html );

        }

        public function getTranslationStrings(){
            return array();
        }

    }
