<?php

class Doctrine_Template_Translatable extends Doctrine_Template{

    protected $_options = array(
        'languages' => array() ,
        'fields'    => array() ,
        'managed'   => true ,
    );

    public function setTableDefinition(){

        $active_language = $this->getActiveLanguage();

        foreach( $this->getFields() as $field => $options ){
            foreach( $this->getLanguages() as $language ) {
                $real_field_name = $this->getRealFieldName( $field , $language );

                $options = (array) $options;
                if( !isset( $options['type']   ) ) $options['type']   = 'string';
                if( !isset( $options['length'] ) ) $options['length'] = null;

                if( $language == $active_language )
                    $this->hasColumn( $real_field_name . " as ${field}"  , $options['type']  , $options['length'] , $options );

                $this->hasColumn( $real_field_name , $options['type']  , $options['length'] , $options );

            }
        }
    }

    public function setUp(){
        $this->_table->unshiftFilter(new Doctrine_Template_Translatable_Filter() );
        $this->addListener(new Doctrine_Template_Listener_Translatable());
    }

    /* = Is Managed ========================== */
    protected function isManaged(){
        return (bool)$this->_options['managed'];
    }

    public function isTranslatableManaged(){
        return $this->isManaged();
    }

    public function isTranslatableManagedTableProxy(){
        return $this->isManaged();
    }

    /* = Get Fields ========================== */
    protected function getFields(){

        static $normalized = false;

        if( !$normalized ){
            foreach( $this->_options['fields'] as $field => $options ){
               $this->_options['fields'][ $field ]['type']   = isset( $options['type'] ) ? $options['type']      : 'string';
               $this->_options['fields'][ $field ]['length'] = isset( $options['length'] ) ? $options['length']  : null;
            }
            $normalized = true;
        }

        return $this->_options['fields'];
    }

    public function getTranslatableFields(){
        return $this->getFields();
    }

    public function getTranslatableFieldsTableProxy(){
        return $this->getFields();
    }

    /* = Get Languages ========================== */
    protected function getLanguages(){

        static $normalized = false;

        if( !$normalized ){
            $this->_options['languages'] = (array)$this->_options['languages'];
            $normalized = true;
        }

        return $this->_options['languages'];
    }

    public function getTranslatableLanguages(){
        return $this->getLanguages();
    }

    public function getTranslatableLanguagesTableProxy(){
        return $this->getLanguages();
    }

    /* = Get Default Language ========================== */
    protected function getDefaultLanguage(){
        $languages = $this->getLanguages();
        return array_shift( $languages );
    }

    public function getTranslatableDefaultLanguage(){
        return $this->getDefaultLanguage();
    }

    public function getTranslatableDefaultLanguageTableProxy(){
        return $this->getDefaultLanguage();
    }

    /* = Get Active Language ============================== */
    protected function getActiveLanguage(){

        $language = Translate::getLanguage();
        if( $this->isLanguage( $language ) )
            return $language;
        else
            return $this->getDefaultLanguage();

    }

    public function getTranslatableActiveLanguage(){
        return $this->getActiveLanguage();
    }

    public function getTranslatableActiveLanguageTableProxy(){
        return $this->getActiveLanguage();
    }

    /* = isField ========================================== */
    protected function isField( $field ){
        $fields = $this->getFields();
        $fields = array_keys( $fields );
        return in_array( $field , $fields );
    }

    public function isTranslatableField( $field ){
        return $this->isField( $field );
    }

    public function isTranslatableFieldTableProxy( $field ){
        return $this->isField( $field );
    }

    /* = isLanguage ========================================== */
    protected function isLanguage( $language ){
        $langs = $this->getLanguages();
        return in_array( $language , $langs );
    }

    public function isTranslatableLanguage( $language ){
        return $this->isLanguage( $language );
    }

    public function isTranslatableLanguageTableProxy( $language ){
        return $this->isLanguage( $language );
    }

    /* = getRealFieldName ==================================== */
   public function getRealFieldName( $field , $language = null ){

        if( $language === null )
            $language = $this->getActiveLanguage();

        if( !$this->isField( $field ) )
            return null;

        if( !$this->isLanguage( $language ) )
            return null;

        return "{$field}_{$language}";

    }

    public function getTranslatableRealFieldName( $field , $language = null ){
        return $this->getRealFieldName( $field , $language );
    }

    public function getTranslatableRealFieldNameTableProxy( $field , $language = null ){
        return $this->getRealFieldName( $field , $language );
    }

    protected function getTranslatedValue( $field , $record ){

        if( !$this->isField( $field ) )
            throw new Doctrine_Template_Translatable_Exception('Invalid Field $field');

        $active_language    = $this->getActiveLanguage( $field  );
        $active_field       = $this->getRealFieldName( $field , $active_language  );

        //Get the value
        $value = null;
        if( isset( $record[ $active_field ] ) && $record[ $active_field ] != '' ){
            $value = $record[ $active_field ];
        }else{
            $languages = $this->getLanguages();
            foreach( $languages as $language ){
                $real_field_name = $this->getRealFieldName( $field , $language );
                if( isset( $record[ $real_field_name ] ) && $record[ $real_field_name ] != '' ){
                    $value = $record[ $real_field_name ];
                    break;
                }
            }
        }

        return $value;

    }

    public function getTranslatableValue( $field ){
        return $this->getTranslatedValue( $field , $this->getInvoker() );
    }

    public function getTranslatableValueTableProxy( $field , $record ){
        return $this->getTranslatedValue( $field , $record );
    }

    protected function getTranslatedValues( $record ){

        $values = array();
        $fields = $this->getFields();
        foreach( $fields as $field => $options ){
            try{
                $values[ $field ] = $this->getTranslatedValue( $field , $record );
            }catch( Doctrine_Template_Translatable_Exception $ex ){}
        }

        return $values;

    }

    public function getTranslatableValues( ){
        return $this->getTranslatedValues( $this->getInvoker() );
    }

    public function getTranslatableValuesTableProxy( $record ){
        return $this->getTranslatedValues( $record );
    }

}

class Doctrine_Template_Translatable_Exception extends Exception{

}

class Doctrine_Template_Translatable_Filter extends Doctrine_Record_Filter{

    public function filterSet(Doctrine_Record $record, $name, $value){

        //Make sure its a valid field
        if( !$record->isTranslatableField( $name ) )
            throw new Doctrine_Record_UnknownPropertyException(sprintf('Unknown record property / related component "%s" on "%s"', $name, get_class($record)));

        //Get the real field to set
        $real_field = $record->getTranslatableRealFieldName( $name );

        //Set the value
        $record[ $real_field ] = $value;

    }

    public function filterGet(Doctrine_Record $record, $name){

        //Make sure its a valid field
        if( !$record->isTranslatableField( $name ) )
            throw new Doctrine_Record_UnknownPropertyException(sprintf('Unknown record property / related component "%s" on "%s"', $name, get_class($record)));

        //Get the value
        return $record->getTranslatableValue( $name );

    }

}
