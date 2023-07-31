<?php

    class Helper_Form_Element_CheckBoxes extends Helper_Form_Element_Multiple{

        public function setupOptions()
        {
            parent::setupOptions();
            $this->addOption("disabled" , null );
            $this->addOption("inline" , false   );
        }

        public function renderStart()
        {
            $domid    = $this->getDomId();
            $this->addAttribute( 'id' , $domid );

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

            return Html::start( 'div' , $attributes );
        }

		public function renderBody( )
		{

            //Render the element
            $domid    = $this->getDomId();
            $domname  = $this->getDomName();
            $disabled = $this->getOption('disabled');
            $inline   = $this->getOption('inline');
            $empty    = $this->getOption('empty');
            $values   = $this->getValue();

            //Empty value
            $this->setOption("empty" , null);

            //Get possible values
            $possibles  = $this->getPossibleValues();

            $content   = array();
            foreach( $possibles as $value => $label ){

                $container_classes = array();
                $container_classes[] = "ym-form-checkboxes-container";
                $container_classes[] = $inline ? "ym-form-checkboxes-container-inline" : "";
                $container_classes   = implode( " " , $container_classes );
                $checkbox_id         = $domid."_".$value;
                $checkbox_name       = $domname."[".$value."]";
                $checkbox_checked    = in_array( $value , $values ) ? "checked='checked'" : "";
                $checkbox_disabled   = $disabled ? "disabled='disabled'" : "";

                $content[] = "<div class='$container_classes'>";
                $content[] = "  <input type='checkbox' id='$checkbox_id' name='$checkbox_name' value='$value' $checkbox_checked $checkbox_disabled />";
                $content[] = "  <label for='$checkbox_id'> $label </label>";
                $content[] = "</div>";

            }

            if (count($possibles) === 0) {
                $content[] = t($empty);
            }

            return implode( "\n" , $content );
        }

        public function renderEnd()
        {
            return Html::end( 'div');
        }



    }
