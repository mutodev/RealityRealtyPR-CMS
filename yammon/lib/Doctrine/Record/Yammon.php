<?php

abstract class Doctrine_Record_Yammon extends Doctrine_Record{

    public function toArray( $deep = true, $prefixKey = false ){
        $array = parent::toArray( $deep , $prefixKey );
        if( $this->_table->hasTemplate('Translatable') ){
            $values = $this->_table->getTranslatableValues( $array );
            foreach( $values as $k => $v ){
                $array[ $k ] = $v;
            }
        }
        return $array;
    }

    public function synchronizeWithArray( array $array , $deep = true ){
        return $this->sync( $array , $deep );
    }

    public function sync( array $array , $deep = true ){

        $table = $this->getTable();
        $identifiers = array();

        foreach( $array as $field => $value ){

            //Get the definition for the field
            $definition = $table->getColumnDefinition( $field );

            //Convert empty string to null on integers ( because of fk constraints )
            //and dates ( because of the 0 date problem )
            if($definition) {

                if (in_array($definition['type'] , array('integer' , 'float' , 'decimal' , 'timestamp' , 'date' , 'time')) && is_string($value) && trim($value) === ''){
                    $array[$field] = null;
                }

                if (in_array($field, (array)$table->getIdentifier())) {
                    $identifiers[$field] = $value;
                }
            }

            //Sync relations without doing any additional queries
            if ($deep && $this->getTable()->hasRelation($field)) {

                $rel = $this->getTable()->getRelation($field);
                $class = $rel->getClass();

                if (!$array[$field]) {
                    unset($array[$field]);
                }

                else if ($rel->getType() == Doctrine_Relation::ONE) {

                    $object = new $class;
                    $object->sync($array[$field]);
                    $this->$field = $object;
                }

                else if ($rel->getType() == Doctrine_Relation::MANY) {

                    $collection = new Doctrine_Collection_Yammon($class);

                    foreach($array[$field] as $key => $val) {

                        if (!$val) {
                            unset($array[$field][$key]);
                            continue;
                        }

                        $object = new $class;
                        $object->sync($val);
                        $collection->add($object, $key);
                    }

                    $this->$field = $collection;
                }
            }

        }

        //Assign identifiers
        $identifiers = array_filter($identifiers);

        if ($identifiers) {
            $this->assignIdentifier($identifiers);
        }

        //Call default synchronizeWithArray
        return parent::synchronizeWithArray( $array , $deep );

    }

    public function syncAndSave( array $array ){
        return self::syncAndSaveFromForm( $this , $array );
    }

    static public function syncAndSaveFromForm($Obj, array $array){

        $table     = $Obj->getTable();
        $relations = array();

        //Mon additional Sync
        foreach( $array as $field => $value ){

            //Get the definition for the field
            $definition = $table->getColumnDefinition( $field );

            //If its not a field ignore it
            if( !$definition )
                continue;

            //Convert empty string to null on integers ( because of fk constraints )
            //and dates ( because of the 0 date problem )
            if( in_array(  $definition['type'] , array('integer' , 'float' , 'decimal' , 'timestamp' , 'date' , 'time' ) ) ){
                if( is_string($value) && trim($value) === '' ){
                    $array[ $field ] = null;
                }
            }

        }

        //Doctrine synchronizeWithArray without deep
        $refresh   = false;
        foreach ($array as $key => $value) {
            if ($key == '_identifier') {
                $refresh = true;
                $Obj->assignIdentifier($value);
                continue;
            }

            if ( $table->hasRelation($key) ) {

                $rel = $table->getRelation($key);

                //MANY_TO_ONE
                if ( $rel->isOneToOne() && !is_array($value) ) {

                    $relationInfo  = $rel->toArray();
                    $localColumn   = $relationInfo['local'];

                    $Obj->$localColumn = $value;
                }

                //OTHERS RELATIONS
                else {
                    $relations[$key] = $value;
                    unset($array[$key]);
                }

            }
            else if ($table->hasField($key) || array_key_exists($key, $Obj->_values)) {
                $Obj->set($key, $value);
            }
        }

        if ($refresh) {
            $Obj->refresh();
        }


        //Daniel relations synchronize
        //Save Obj
        $Obj->resetPendingUnlinks();
        $Obj->save();

        //Relations
        foreach ($relations as $key => $value) {

            if ( !$Obj->$key) {
                $Obj->refreshRelated($key);
            }

            //Get Relation Information
            $rel           = $table->getRelation($key);
            $relationInfo  = $rel->toArray();
            $localColumn   = $relationInfo['local'];
            $foreignColumn = $relationInfo['foreign'];

            //MANY_TO_MANY and ONE_TO_MANY relationships
            if ( !$rel->isOneToOne() ) {


                //MANY_TO_MANY
                if ( $relationInfo['refTable'] ) {

                    $arrayValues       = (array) $value;
                    $relRef            = $relationInfo['refTable']->getComponentName();
                    $relatedObjsValues = $Obj->$relRef->toArray();

                    //Ya no existen (Se borran)
                    foreach ( $Obj->$relRef as $relatedObj ) {

                        if ( ($rKey = array_search( $relatedObj->$foreignColumn, $arrayValues )) === false ) {
                            //pr($key, 'DELETE');
                            $relatedObj->delete();
                        }

                        //Ya existe y no queremos anadirlo
                        else
                            unset($arrayValues[$rKey]);

                    }

                    //No existe (Anadirlo)
                    foreach( $arrayValues as $arrayValue ) {

                        //pr($key, 'ANADIR');

                        $refTableRelationInfo = $table->getRelation($relRef)->toArray();

                        $relatedObj = new $relRef;
                        $relatedObj->$localColumn   = $Obj->{$refTableRelationInfo['local']};
                        $relatedObj->$foreignColumn = $arrayValue;
                        $relatedObj->save();
                    }


                }
                //ONE_TO_MANY
                else {

                    //Update the relation with the local column value
                    foreach( (array)$value as $tKey => $tValue )
                        if ( $Obj->getTable()->hasField($localColumn) )
                            $value[$tKey][$foreignColumn] = $Obj->$localColumn;

                    $arrayValues   = (array) $value;
                    $update_objs   = array();
                    $relationTable = Doctrine::getTable( $relationInfo['class'] );
                    $primaryKeys   = (array) $relationTable->getIdentifier();

                    //Ya no existen (Se borran)
                    foreach ( $Obj->$key as $relatedObj ) {

                        $primaryKeysValues = array_intersect_key( $relatedObj->toArray(), array_flip($primaryKeys) );

                        if ( ($rKey = self::array_search_partial( $primaryKeysValues, $arrayValues)) === false ) {
                            //pr($key, 'DELETE', $primaryKeysValues);
                            $relatedObj->delete();

                        }

                        //Ya existe y no queremos anadirlo, queremos actualizarlo
                        else
                            $update_objs[$rKey] = $relatedObj;

                    }

                    //Anadirlo o modificarlo
                    foreach( $arrayValues as $rKey => $arrayValue ) {

                        if ( isset($update_objs[$rKey]) ) {
                            $relatedObj = $update_objs[$rKey];
                            //pr($key, 'MODIFICAR', $arrayValue);
                        }
                        else {
                            $relatedObj = new $relationInfo['class'];
                            //pr($key, 'ANADIR', $arrayValue);
                        }

                        self::syncAndSaveFromForm( $relatedObj, $arrayValue );
                    }

                }



            }
            //ONE_TO_ONE relationships ( MANY_TO_ONE is already down before the save() )
            else {

                //Update the relation with the local column value
                foreach( (array)$value as $tKey => $tValue )
                    if ( $Obj->getTable()->hasField($localColumn) )
                        $value[$foreignColumn] = $Obj->$localColumn;

                self::syncAndSaveFromForm( $Obj->$key, $value );

            }


        }

    }

    //Function to search for an array in a multi-dimensional array,
    //But only the keys we wanted, not all keys
    static function array_search_partial($needle, $haystack) {

        if (empty($needle) || empty($haystack)) {
            return false;
        }

        foreach ($haystack as $key => $value) {
            $exists = 0;
            foreach ($needle as $nkey => $nvalue) {
                if (!empty($value[$nkey]) && $value[$nkey] == $nvalue) {
                    $exists = 1;
                } else {
                    $exists = 0;
                }
            }
            if ($exists) return $key;
        }

        return false;
    }

}
