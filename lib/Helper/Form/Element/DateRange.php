<?php

    class Helper_Form_Element_DateRange extends Helper_Form_Element_SubForm
    {

        public function __construct( $name , $options = array() , Helper_Form_Element_Container $parent = null )
        {
            parent::__construct( $name , $options , $parent );

            $dateElementOptions = array(
                'type'     => 'DateTime',
                'label'    => '',
                'style'    => 'width:100%' ,
                'colwidth' => '49%',
                'split'    => false,
                'format'   => '%Y-%m-%d %p',
                'output'   => 'Y-m-d'
            );

            $this->add( array('name'=> 'date_from') + $dateElementOptions );

            $this->add( array(
                'name'     => 'content',
                'content'  => '&nbsp;&nbsp;'.t('to').'&nbsp;',
                'colwidth' => '2%'
            ));

            $this->add( array('name'=> 'date_to')   + $dateElementOptions );
        }

        public function setupOptions()
        {
            parent::setupOptions();

            $this->setOption('layout_renderer'   , 'horizontal');
            $this->setOption('default_renderers' , array(
                'box_renderer' => array(
                    'type'    => '1Column',
                    'class'   => 'daterange-date',
                    'margin'  => '0px !important',
                    'padding' => '0px !important',
                    'border'  => '0px !important',

                    )
                )
            );
        }

        public function getValue()
        {
            $value = parent::getValue();
            $value = implode('!', $value );

            return  $value == '!' ? null : $value;
        }

    }
