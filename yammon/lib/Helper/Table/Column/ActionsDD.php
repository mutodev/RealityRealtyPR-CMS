<?php

    class Helper_Table_Column_ActionsDD extends Helper_Table_Column_Actions{

        public function __construct( $parent , $name , $options = array() ){
            parent::__construct( $parent , $name , $options );

            $JS  = helper('Javascript');
            $CSS = helper('Css');

            $JS->add('/yammon/public/widget/widget.js');
            $JS->add('/yammon/public/widget/dropdownlinks/dropdownlinks.js');
            $CSS->add('/yammon/public/widget/dropdownlinks/dropdownlinks.css');

        }

        public function text( $record ){

            $Html    = new Html();
            $Html->open('div' , array('widget' => 'DropDownLinks' )  );
                $Html->text( parent::text( $record ) );
            $Html->close('div');

            return $Html->get();

        }

    }
