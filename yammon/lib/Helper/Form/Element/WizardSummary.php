<?php

class Helper_Form_Element_WizardSummary extends Helper_Form_Element
{
    public function setupOptions()
    {
        parent::setupOptions();
        $this->addOption("wizard_element");
        $this->addOption("step_number_template", '%{number}' );
        $this->addOption("step_text_template"  , '%{label}'  );
        $this->addOption("step_template" , '<span class="step-number">%{number}</span><span class="step-text">%{text}</span>' );

        $this->setOption("box_renderer" , 'NoBox' );
    }

    public function construct(){

        parent::construct();

        $Css = helper('Css');
        $Css->add("/yammon/public/form/css/wizardsummary.css");
    }

    function render( $opts = array() )
    {
        $wizard_element       = $this->getOption("wizard_element");
        $step_number_template = $this->getOption("step_number_template");
        $step_text_template   = $this->getOption("step_text_template");
        $step_template        = $this->getOption("step_template");

        //Get Elements
        $Wizard = $this->getRelative($wizard_element);
        $Steps  = $Wizard->getElements();

        $this->addClass('ym-form-wizardsummary');
        $attributes = $this->getAttributes();

        $content   = array();
        $content[] = "<div $attributes>";
        $content[]   = "<table border='0' cellspacing='0' cellpadding'0'>";
        $content[]     = "<tr>";

        $position     = 1;
        $stepSelected = $Wizard->getStep() + 1;
        $count        = count( $Steps );
        $width        = 100 / $count;
        foreach( $Steps as $name => $Step ) {

            $templateVars = array();
            $classess     = array();

            if ( $position == 1 )             $classess[] = 'first';
            if ( $position == count($Steps) ) $classess[] = 'last';

            if ( $position < $stepSelected )  $classess[] = 'visited';
            if ( $position > $stepSelected )  $classess[] = 'upcoming';
            if ( $position == $stepSelected ) $classess[] = 'current';

            $templateVars['number'] = Template::create( $step_number_template )->apply( array('number' => $position) );
            $templateVars['text']   = Template::create( $step_text_template )->apply( array(
                'label'       => $Step->getLabel(),
                'description' => $Step->getDescription(),
                'example'     => $Step->getExample(),
            ));

            $content[]   = "<td class='".implode(' ', $classess)."' style='width:{$width}%'>";
            $content[]     = Template::create( $step_template )->apply( $templateVars );
            $content[]   = "</td>";

            $position++;
        }

        $content[]     = "</tr>";
        $content[]   = "</table>";
        $content[] = "</div>";

        return implode("\n" , $content );
    }
}