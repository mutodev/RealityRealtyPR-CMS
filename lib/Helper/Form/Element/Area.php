<?php

    class Helper_Form_Element_Area extends Helper_Form_Element_SubForm{

        protected $current_container;
        protected $area_types = array('country', 'region', 'state', 'county', 'city', 'area');

        public function __construct( $name , $options = array() , Helper_Form_Element_Container $parent = null )
        {
            parent::__construct( $name , $options , $parent );

            $areaTypes = $this->getAreaTypes();
            $required  = (bool) $this->getOption('required');

            foreach( $areaTypes as $areaType )
                $this->createAreaElement( $areaType );

            $this->setOption( 'class' , '' );
            $this->setOption( 'label' , '' );
            $this->setOption( 'required' , false );
        }

        public function setupOptions()
        {
            parent::setupOptions();

            $this->setOption('layout_renderer' , 'vertical');
            $this->setOption('box_renderer' , array(
                'type'    => '1Column' ,
                'margin'  => 0 ,
                'padding' => 0 ,
                'border'  => true ,
            ));

            $this->addOption('merge_remaining', true );
            $this->addOption('area_types' , array('country', 'state', 'city'));
        }

        public function getAreaTypeValues( $type )
        {
            $return          = array();
            $areaTypes       = $this->getAreaTypes();
            $areaAncestors   = array_slice( $areaTypes, 0, array_search($type, $areaTypes) );
            $areaDescendants = $type;

            if ( $this->getAreaElement($type) == $this->getAreaValueElement( $type ) && $this->getOption('merge_remaining') ) {
                $areaDescendants = array_slice( $this->area_types, array_search($type, $this->area_types) );
            }

            $q = new Doctrine_Query();
            $q->select( 'Area.id, Area.name, Area.type' );
            $q->from('Area');

            $q->andWhereIn('type', (array) $areaDescendants );

            foreach( (array) $areaDescendants as $areaDescendant )
                $q->addOrderBy("Area.$areaDescendant ASC");

            foreach( $areaAncestors as $areaAncestor ) {
                if ( $value = $this->getAreaElement($areaAncestor)->getValue() )
                    $q->andWhere("{$areaAncestor}_id = ?", $value );
                else
                    $q->andWhere("{$areaAncestor}_id IS NULL" );
            }

            foreach( $q->fetchArray() as $result ){
                $level    = array_search( $result['type'] , (array) $areaDescendants);
                $prelabel = ($level > 0) ? str_repeat('&nbsp;&nbsp;', $level).'-&nbsp;' : '';

                $return[ $result['id'] ] = $prelabel . $result['name'];
            }

            return $return;
        }

        public function dependencyCallback( $type )
        {
            $area_types   = $this->getAreaTypes();
            $area_depends = array_slice( $area_types, 0, array_search($type, $area_types) );

            $q = new Doctrine_Query();
            $q->from('Area');
            $q->andWhere('type = ?' , $type );

            foreach( $area_depends as $area_depend ) {
                $column = $area_depend.'_id';
                $value  = $this->getAreaElement( $area_depend )->getValue();

                if ( $value )
                    $q->andWhere("$column = ?", $value);
                else
                    $q->andWhere("$column IS NULL");
            }

            return (bool) $q->count();
        }

        public function setValue( $value )
        {
            if( $value === null )
                return;

            $q = new Doctrine_Query();
            $q->from('Area');
            $q->andWhere( 'Area.id = ?' , $value );
            $obj = $q->fetchOne();

            if( !$obj )
                return;

            //Object Values
            $areaValues   = array();
            $areaLastType = null;
            foreach( $this->getAreaTypes() as $areaType ) {

                $value = ( $areaType == $obj->type ) ? $obj->id : $obj->{$areaType.'_id'};

                if ( $value ) {
                    $this->getAreaElement($areaType)->setValue( $value );

                    $areaLastType = $areaType;
                }
            }
            $this->getAreaElement($areaLastType)->setValue( $obj->id );
        }

        public function setDefaultValue( $value )
        {
            if( $value === null )
                return;

            $q = new Doctrine_Query();
            $q->from('Area');
            $q->andWhere( 'Area.id = ?' , $value );
            $obj = $q->fetchOne();

            if( !$obj )
                return;

            //Object Values
            $areaValues   = array();
            $areaLastType = null;
            foreach( $this->getAreaTypes() as $areaType ) {

                $value = ( $areaType == $obj->type ) ? $obj->id : $obj->{$areaType.'_id'};

                if ( $value ) {
                    $this->getAreaElement($areaType)->setDefaultValue( $value );

                    $areaLastType = $areaType;
                }
            }
            $this->getAreaElement($areaLastType)->setDefaultValue( $obj->id );
        }

        public function isPresent( $value )
        {
            return $this->getAreaValueElement()->isPresent( $value );
        }

        public function getValue()
        {
            return $this->getAreaValueElement()->getValue();
        }

        public function getUnfilteredValue()
        {
            return $this->getAreaValueElement()->getUnfilteredValue();
        }

        public function isValid()
        {
            //Check if the element already has been invalidated
            if( $this->error !== null )
                return false;

            $AreaValueElement = $this->getAreaValueElement();
            $type             = $AreaValueElement->getName();
            $value            = $AreaValueElement->getValue();

            if ( !$value )
                return parent::isValid();

            $q = new Doctrine_Query();
            $q->from('Area');
            $q->where('(type = ? AND id = ?)', array($type, $value) );

            if ( $type != 'area' && $this->getOption('merge_remaining') )
                $q->orWhere("({$type}_id IS NOT NULL AND id = ?)", $value);

            if ( !(bool) $q->count() ) {
                $template    = new Template( t("%{label} is invalid") );
                $this->error = $template->apply( array('label' => $AreaValueElement->getLabel()) );

                return false;
            }

            return parent::isValid();
        }

        protected function createAreaElement( $type )
        {
            $nested      = array();
            $depends     = array();
            $required    = (bool) $this->getOption('required');
            $class       = $this->getOption('class');
            $default     = $this->getOption('default');
            $areaTypes   = $this->getAreaTypes();
            $parentAreas = array_slice( $areaTypes, 0, array_search($type, $areaTypes) );

            foreach( $parentAreas as $parentArea ) {

                //Nested
                $nested[] = ".{$parentArea}";

                //Depends
                $depends[".{$parentArea}"] = array( 'callback' => array( $this, "show".ucfirst($type)."Element" ) );
            }

            $this->add( array(
                'type'            => 'Select',
                'name'            => $type ,
                'label'           => t( ucfirst($type) ) ,
                'style'           => 'width:100%' ,
                'class'           => $class ,
                'required'        => $required ,
                'default'         => $default ,
                'tags'            => $this->getTags() ,
                'source_callback' => array( $this , "get".ucfirst($type)."Values" ) ,
                'source_nested'   => $nested ,
                'source_validate' => false,
                'depends'         => $depends,
            ));
        }

        protected function getAreaTypes()
        {
            return array_intersect( (array) $this->getOption('area_types'), $this->area_types );
        }

        protected function getAreaValueElement( $updateFromType = null )
        {
            $areaTypes = $this->getAreaTypes();
            $first     = $areaTypes[0];
            $last      = $areaTypes[ count($areaTypes) - 1 ];

            if ( !$this->getAreaElement($first)->getValue() )
                return $this->getAreaElement($last);

            $q = new Doctrine_Query();
            $q->select( 'Area.type' );
            $q->from('Area');
            $q->orderBy('Area.level DESC , Area.name');
            $q->andWhereIn('type', (array) $areaTypes );
            $q->limit(1);

            foreach( $areaTypes as $areaType ) {

                //Only use parent types
                if ( $updateFromType == $areaType )
                    break;

                if ( $value = $this->getAreaElement($areaType)->getValue() ) {
                    $q->addWhere("{$areaType}_id = ?", $value);

                    $last = $areaType;
                }
            }

            $key = implode('|', $q->getFlattenedParams() );
            static $cache = array();
            if( isset( $cache[ $key ] ) )
                return $cache[ $key ];

            $type = $q->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

            //Use the previous Area Type
            if ( !$type )
                $type = $last;

            return $cache[ $key ] = $this->getAreaElement($type);
        }

        public function getCountryValues( $element )
        {
            $this->setAreaContainer( $element->getParent() );

            return $this->getAreaTypeValues('country');
        }

        public function getRegionValues( $element )
        {
            $this->setAreaContainer( $element->getParent() );

            return $this->getAreaTypeValues('region');
        }

        public function getStateValues( $element )
        {
            $this->setAreaContainer( $element->getParent() );

            return $this->getAreaTypeValues('state');
        }

        public function getCountyValues( $element )
        {
            $this->setAreaContainer( $element->getParent() );

            return $this->getAreaTypeValues('county');
        }

        public function getCityValues( $element )
        {
            $this->setAreaContainer( $element->getParent() );

            return $this->getAreaTypeValues('city');
        }

        public function getAreaValues( $element )
        {
            $this->setAreaContainer( $element->getParent() );

            return $this->getAreaTypeValues('area');
        }

        public function showRegionElement( $element , $value )
        {
            $this->setAreaContainer( $element->getParent() );

            return $this->dependencyCallback('region');
        }

        public function showStateElement( $element , $value )
        {
            $this->setAreaContainer( $element->getParent() );

            return $this->dependencyCallback('state');
        }

        public function showCountyElement( $element , $value )
        {
            $this->setAreaContainer( $element->getParent() );

            return $this->dependencyCallback('county');
        }

        public function showCityElement( $element , $value )
        {
            $this->setAreaContainer( $element->getParent() );

            return $this->dependencyCallback('city');
        }

        public function showAreaElement( $element , $value )
        {
            $this->setAreaContainer( $element->getParent() );

            return $this->dependencyCallback('area');
        }

        public function setAreaContainer( $container )
        {
            $this->current_container = $container;
        }

        public function getAreaElement( $type )
        {
            if ( !is_null($this->current_container) )
                return $this->current_container->get( $type );

            return $this->get( $type );
        }
    }
