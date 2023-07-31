<?php

    //Get the helpers
    $Flash  = helper('Flash');
    $Form   = helper('Form');

    //Get the arguments
    $id       = get('id');
    $edit     = !empty( $id );

    //Define constants
    $REDIRECT_URL = url("...");
    $MO_FILE      = Yammon::getWritablePath('locale') . $id . '.mo';

    //Retrieve language data
    if ($edit && file_exists($MO_FILE) ) {
        $Gettext_MO = new Gettext($MO_FILE);
        $Gettext_MO->load();
    }
    else if ($edit) {
        $Flash->error( t('The language was not found' ) , $REDIRECT_URL );
    }
    else {
        $Gettext_MO = new Gettext_MO('');
    }

    //Load the Values
    if( !$Form->isSubmitted() ){
        
        //Set Form Values
        if( $values ){
            $Form->setValues( $values );
        }
    }

    //Validate form
    if( $Form->isValid() ){

        $values = $Form->getValues();

        //Meta data
        $Gettext_MO->meta = array();
        $Gettext_MO['Content-Type'] = 'text/plain; charset='.$values['charset'];
        $Gettext_MO['Language']     = $values['language'];
        $Gettext_MO['Code']         = $values['code'];

        $Gettext_MO->save();

        
    }

    //Set variables
    Action::set('edit'   , $edit   );