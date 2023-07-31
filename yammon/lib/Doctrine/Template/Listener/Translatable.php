<?php

class Doctrine_Template_Listener_Translatable extends Doctrine_Record_Listener
{
    public function preDqlSelect(Doctrine_Event $event)
    {
        $invoker = $event->getInvoker();
        $params  = $event->getParams();
        $alias   = $params['alias'];
        $query   = $event->getQuery();

        $languages = $invoker->getTranslatableLanguages();
        $fields    = $invoker->getTranslatableFields();

        if( $query->contains('SELECT') ){
            foreach( $fields as $field => $options ){

                //Add translation fields only when the field is in the SELECT
                if (!in_array("$alias.$field", $query->getDqlPart('select'))) {
                    continue;
                }

                foreach( $languages as $language ){

                    $real_field_name = $alias . '.' . $invoker->getRealFieldName( $field , $language );
                    $query->addSelect( $real_field_name );
                }
            }
        }
    }

    public function postHydrate(Doctrine_Event $event){

        $invoker= $event->getInvoker();
        $data   = $event->data;

        $values = $invoker->getTranslatableValues( $data );

        foreach( $values as $k => $v ){
            $data[ $k ] = $v;
        }
        $event->data = $data;

    }
}

