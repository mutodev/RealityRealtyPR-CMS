<?php

    class Translate_Format_DB extends Translate_Format{

        public static function load( $filename )
        {

                $strings = array();

                //Get the language out of the filename
                $requested_lang = basename( $filename , ".db" );

                ///Get the list of translatable models
                $models = Doctrine::loadModels( DOCTRINE_MODELS_PATH );
                foreach( $models as $model ){

                    //Filter out base classes
                    $reflection = new ReflectionClass( $model );
                    if( $reflection->isAbstract() ){
                        continue;
                    }

                    if( !$reflection->isSubclassOf('Doctrine_Record') ){
                        continue;
                    }

                    //Filter out models without translation
                    $table    = Doctrine::getTable( $model );
                    if( !$table->hasTemplate('Doctrine_Template_Translatable') ){
                        continue;
                    }

                    //Get the template
                    $template = $table->getTemplate('Doctrine_Template_Translatable');

                    //Get template languages and fields
                    $langs        = $template->getTranslatableLanguages();
                    $fields       = array_keys($template->getTranslatableFields() );
                    $managed      = $template->isTranslatableManaged();
                    $identifiers  = $table->getIdentifierColumnNames();
                    $default_lang = current( $langs );

                    //Filter out the models that are not translated by that language

                    //Filter out if its is not managed
                    if( !$managed ){
                        continue;
                    }

                    //Filter out the models that dont have fields
                    if( empty( $fields ) ){
                        continue;
                    }

                    //Create query to load strings
                    $q = new Doctrine_Query();

                    foreach( $identifiers as $identifier )
                        $q->addSelect( $identifier );

                    foreach( $fields as $field )
                        $q->addSelect( $field );

                    $q->from( $model );

                    //Load the strings
                    try{
                        @$result = $q->fetchArray();
                    }catch( Exception $ex ){
                        $result  = array();
                    }

                    //Convert to string format
                    foreach( $result as $k => $v ){


                        foreach( $fields as $field ){

                            //Get the string
                            $string      = $result[$k][$field.'_'.$default_lang];

                            //Get the translation
                            $translation = trim($result[$k][$field.'_'.$requested_lang]);
                            if( $translation == '' )
                                $translation = null;

                            //Get the location
                            $location_ids   = array();
                            foreach( $identifiers as $identifier )
                                $location_ids[] = $result[$k][ $identifier ];
                            $location_ids = implode( ":" , $location_ids  );

                            $strings[ $string ]['translation'] = $translation;
                            $strings[ $string ]['locations'][] = array( $model .".". $field , $location_ids );

                        }

                    }


                }

                return new Translate_Strings( $strings );
        }

        public static function save( Translate_Strings $strings , $filename )
        {

            //Get the language out of the filename
            $requested_lang = basename( $filename , ".db" );

            //Get Modified Strings
            $modified = $strings->getModified();

            //Translate strings in database
            foreach( $modified as $string ){
                $translation = $strings->get( $string );
                $locations   = $strings->getLocations( $string );

                foreach( $locations as $location ){

                    list($location_model , $location_field ) = explode( "." , $location[0] );
                    $location_ids    = explode(":" , $location[1] );

                    //Load Model
                    $obj = Doctrine::getTable( $location_model )->find( $location_ids );

                    //Update
                    $obj[ $location_field . '_' . $requested_lang ] = $translation;

                    //Save
                    $obj->save();


                }

            }


        }

    }