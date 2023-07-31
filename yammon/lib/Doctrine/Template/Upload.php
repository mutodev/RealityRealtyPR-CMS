<?php

class Doctrine_Template_Upload extends Doctrine_Template
{

    protected $_options = array( 'fields' => array() );

    /**
     * Set table definition for Upload behavior
     *
     * @return void
     */
    public function setTableDefinition()
    {
        foreach( $this->_options['fields'] as $field => $options ) {

            $this->hasColumn($field.'_path as '.$field  , 'string'  , 255);
            $this->hasColumn($field.'_class'            , 'string'  , 255);
            $this->hasColumn($field.'_properties'       , 'string'  , 1000);

            //Additonal searchable columns
            if ( isset($options['columns']) ) {
                foreach( $options['columns'] as $column )
                    $this->hasColumn($field.'_'.$column, 'string'  , 255);
            }

        }

        $this->addListener(new Doctrine_Template_Listener_Upload($this->_options));
    }

}
