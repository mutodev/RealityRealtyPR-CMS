<?php

    class Helper_Form_Element_Radios extends Helper_Form_Element_Sourced{

        public function setupOptions(){
            parent::setupOptions();
            $this->addOption("disabled" , null );
            $this->addOption("inline" , false   );
        }

        public function renderStart()
        {
            $domid    = $this->getDomId();
            $this->addAttribute( 'id' , $domid );
            $attributes = $this->getAttributes( true );
            return Html::start( 'div' , $attributes );

        }

		public function renderBody( ){

            //Render the element
            $domid    = $this->getDomId();
            $domname  = $this->getDomName();
            $disabled = $this->getOption('disabled');
            $inline   = $this->getOption('inline');
            $pvalue   = $this->getValue() ? $this->getValue() : $this->getDefaultValue();


            $possibles  = $this->getPossibleValues();
            $content    = array();
            foreach( $possibles as $value => $label ){

                $container_classes = array();
                $container_classes[] = "ym-form-radios-container";
                $container_classes[] = $inline ? "ym-form-radios-container-inline" : "";
                $container_classes   = implode( " " , $container_classes );
                $radio_id            = $domid."_".$value;
                $radio_name          = $domname;
                $radio_checked       = $pvalue == $value ? "checked='checked'" : "";
                $radio_disabled      = $disabled ? "disabled='disabled'" : "";

                $content[] = "<div class='$container_classes' >";
                $content[] = "  <input type='radio' id='$radio_id' name='$radio_name' value='$value' $radio_checked $radio_disabled />";
                $content[] = "  <label for='$radio_id'> $label </label>";
                $content[] = "</div>";

            }

            return implode( "\n" , $content );

        }

        public function renderEnd()
        {
            return Html::end( 'div');
        }

    }
