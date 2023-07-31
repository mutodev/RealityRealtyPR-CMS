<?php

    class Helper_Form_Element_Select extends Helper_Form_Element_Sourced{

        public function setupOptions(){
            parent::setupOptions();
            $this->addOption( "multiple"   , null );
            $this->addOption( "rows"       , null );
            $this->addOption( "disabled"   , null );
            $this->setOption( "empty"      , t("-- Select One --") );

        }

	    public function renderStart( )
	    {
	        //Get Attributes
            $this->addAttribute( 'id'         , $this->getDomId() );
            $this->addAttribute( 'name'       , $this->getDomName() . ( $this->getOption('multiple') ? '[]' : '' ) );
            $this->addAttribute( 'type'       , $this->getOption('type') );
            $this->addAttribute( 'disabled'   , $this->getOption('disabled') );
            $this->addAttribute( 'rows'       , $this->getOption('rows') );
            $this->addAttribute( 'multiple'   , $this->getOption('multiple') ? 'multiple' : null );

            //Set Nested Attribute
            $nested = (array)$this->getOption('source_nested');
            $nested_subjects = array();
            foreach( $nested as $name ){
                $subject = $this->getRelative( $name );
                if( !$subject ) continue;
                $nested_subjects[] = $subject->getDomId();
            }
            if( $nested_subjects ){
                $nested_str = $this->getFullName().":".implode( "," , $nested_subjects );
                $this->addAttribute( 'ym-form-nested' , $nested_str );
            }

            //Get Attributes
            $attributes = $this->getAttributes( true );

	        //Render Select
            return Html::start( 'select' , $attributes );

	    }

	    public function renderBody()
	    {

		    $Html = new Html();

		    //Get the value
		    $selected = !is_null($this->getValue()) ? $this->getValue() : $this->getDefaultValue();

            //Get the possible values
            $possibles = $this->getPossibleValues();

            foreach( $possibles as $value => $label ){
                if( is_array( $label ) )
                    $this->renderGroup( $selected , $Html  , $value , $label );
                else
                    $this->renderOption( $selected , $Html , $label , $value );
            }

	        return $Html->get();

	    }

        protected function renderGroup( $selected , $Html , $label , $values )
        {
            $attributes = array();
            $attributes['label'] = $label;
            $Html->open('optgroup' , $attributes );
                foreach( $values as $value => $label )
                    $this->renderOption( $selected , $Html , $label , $value );
            $Html->close('optgroup');
        }

        protected function renderOption( $selected , $Html , $label , $value )
        {
            $attributes = array();
            $attributes['value'] = $value;

            if( $this->isSelected( $selected , $value ) )
                $attributes['selected'] = 'selected';

            $Html->open( "option" , $attributes );
            $Html->text( $label );
            $Html->close( 'option' );

        }

	    public function renderEnd()
	    {
            return Html::end( 'select' );
	    }

        protected function isSelected( $selected , $value )
        {

          if ( is_array( $selected ) ){
            return in_array($value, $selected);
          }
          return (string)$selected == (string)$value;
        }


/*        public function build()
        {
            if ($this->getOption('multiple')) {

                helper('Css')->add('/yammon/public/widget/selectcheckbox/selectcheckbox.css');
                helper('Javascript')->add('/yammon/public/widget/selectcheckbox/selectcheckbox.js');
                $this->addAttribute('widget' , 'SelectCheckbox' );
            }
        }*/

    }
