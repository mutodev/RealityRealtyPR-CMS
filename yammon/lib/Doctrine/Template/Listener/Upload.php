<?php

class Doctrine_Template_Listener_Upload extends Doctrine_Record_Listener
{

    protected $_options       = array();
    protected $_previousFiles = array();

    /**
     * __construct
     *
     * @param string $options
     * @return void
     */
    public function __construct(array $options)
    {
        $this->_options = $options;
    }

    /**
     * Set file columns when a record is saved
     *
     * @param Doctrine_Event $event
     * @return void
     */
    public function preSave(Doctrine_Event $event)
    {

        $invoker  = $event->getInvoker();
        $modified = $invoker->getModified();

        foreach( $this->_options['fields'] as $field => $options ) {

            $file = $event->getInvoker()->$field;

            if( !($file instanceof Helper_Form_Element_File_Value)){
                continue;
            }

            //Set default path
            if( !isset( $options['path'] ) ){
                $options['path'] = Yammon::getWritablePath('uploads'.DS.get_class($invoker) );
                $options['path'] = substr($options['path'], strlen(Yammon::getApplicationPath()));
            }

            if ( $file && $ret = $file->save($options['path']) ) {

                $path       = $file->getPath();
                $class      = get_class( $file );
                $properties = $file->getProperties();
            }
            else {

                $identifier   = $this->getUniqueIdentifier( $event->getInvoker()->getTable()->getIdentifier(), $event->getInvoker()->toArray() );
                $previousFile = isset($this->_previousFiles[$field][$identifier]) ? $this->_previousFiles[$field][$identifier] : false;

                if ($previousFile) {
                    $previousFile->delete();
                }

                $path       = null;
                $class      = null;
                $properties = null;
            }

            //Update Fields
            $invoker->$field                 = $path;
            $invoker->{$field.'_class'}      = $class;
            $invoker->{$field.'_properties'} = is_array($properties) ? serialize($properties) : $properties;

            //Additonal searchable columns
            if ( isset($options['columns']) ) {
                foreach( $options['columns'] as $column )
                    $event->getInvoker()->{$field.'_'.$column} = @$properties[$column];
            }

        }
    }

    /**
     * Delete previous files
     *
     * @param Doctrine_Event $event
     * @return void
     */
    public function postUpdate(Doctrine_Event $event)
    {

        foreach( $this->_options['fields'] as $field => $options ) {

            $identifier   = $this->getUniqueIdentifier( $event->getInvoker()->getTable()->getIdentifier(), $event->getInvoker()->toArray() );
            $previousPath = isset($this->_previousFiles[$field][$identifier]) ? $this->_previousFiles[$field][$identifier] : false;
            $currentPath  = $event->getInvoker()->$field;

            //Delete old files
            if ( $previousPath && stripos( (string) $previousPath, (string)$currentPath ) === false )
                $previousPath->delete();
        }
    }

    /**
     * Implement postDelete() hook and delete the uploaded files
     *
     * @param Doctrine_Event $event
     * @return void
     */
    public function postDelete(Doctrine_Event $event)
    {
        foreach( $this->_options['fields'] as $field => $options ) {
            if ( $event->getInvoker()->$field )
                $event->getInvoker()->$field->delete();
        }
    }

    /**
     * Hydrate the file object after save
     *
     * @param Doctrine_Event $event
     * @return void
     */
    public function postSave(Doctrine_Event $event) {

        $identifier   = $this->getUniqueIdentifier( $event->getInvoker()->getTable()->getIdentifier(), $event->getInvoker()->toArray() );
        $this->hydrateFile( $event->getInvoker(), $identifier );
    }

    /**
     * Hydrate the file object
     *
     * @param Doctrine_Event $event
     * @return void
     */
    public function postHydrate(Doctrine_Event $event) {

        if ( is_object($event->data) ) {

            $primaryKeys = $event->data->getTable()->getIdentifier();
            $identifier  = $this->getUniqueIdentifier( $primaryKeys, $event->data->toArray() );
        }
        else {
            $tableName   = $event->getInvoker()->getComponentName();
            $primaryKeys = Doctrine::getTable( $tableName )->getIdentifier();
            $identifier  = $this->getUniqueIdentifier( $primaryKeys, $event->data );
        }

        $this->hydrateFile( $event->data, $identifier );
    }

    protected function hydrateFile( $Obj, $identifier ) {

        $FileSystem = Configure::read("filesystem");

        foreach( $this->_options['fields'] as $field => $options ) {

            //Prevent double hydrating the object
            if ( is_object($Obj[$field])  )
                continue;

            $path       = $Obj[$field];
            $class      = $Obj[$field.'_class'];
            $properties = $Obj[$field.'_properties'];

            //File doesn't exist anymore
            if (!$path || !$class || !$FileSystem->has($path)) {
                $Obj[$field] = null;
                $Obj[$field.'_class'] = null;
                $Obj[$field.'_properties'] = null;
                continue;
            }

            //Convert to object
            $File = new $class( $path );
            $File->setProperties( unserialize($properties) );

            $Obj[$field] = $File;

            $this->_previousFiles[ $field ][ $identifier ] = $File;
        }

    }


    protected function getUniqueIdentifier( $primaryKeys, $values ){

        $primaryKeys       = (array) $primaryKeys;
        $primaryKeysValues = array_intersect_key( $values, array_flip($primaryKeys) );

        return md5( serialize($primaryKeysValues) );
    }
}
