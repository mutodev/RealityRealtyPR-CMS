<?php

    //Get Helpers
    $Form  = helper('Form');
    $Flash = helper('Flash');
    
    //Get Arguments
    $id   = get('id');
    $edit = !empty( $id );

    //Load the permission
    if( !empty( $id ) )
        $Permission = Doctrine::getTable( 'Permission' )->find( $id );
    else
        $Permission = new Permission();
    
    //Make sure the permission exists
    if( empty( $Permission ) )
        $Flash->error( t('Could not find permission for editing') , '..' );

    //Get the possible valid resources
    Doctrine::loadModels( DOCTRINE_MODELS_PATH );
    $models = Doctrine::getLoadedModels();
    asort( $models );
    $models = array_combine( $models , $models );
    $Form->getElement('resource')->setOption( 'options' , $models );

    //Load the values in to the form
    if( $edit ){
        $Form->setValues( $Permission );
    }

    //Validate the form
    if( $Form->isValid() ){

        $values = $Form->getValues();

        $Permission->sync( $values );
        $Permission->save();
                    

        $msg = $edit ? t('Sucessfully updated Permission %{name}' ) : t('Sucessfully created Permission %{name}' );      
        $Flash->success( t( $msg , $Permission ) , '..' );
        
    }


