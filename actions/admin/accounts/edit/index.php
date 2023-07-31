<?php

	$Form  = helper('Form');
	$Flash = helper('Flash');
		
	//Get the arguments
	$id   = get('id');
	$edit = !empty($id );
    
	//Get the model
	$AccountTable = Doctrine::getTable( 'Account' );
	$Account      = $id ? $AccountTable->find( $id ) : new Account();

    //Redirect if we didn't find the account
    if( empty( $Account ) ){
        $Flash->error( t("Coudn't find account." ) ,  "/admin/accounts/" );    
    }

    //Set username and password required if we are
    //in add mode
    if( !$edit ){
        
        $Form->get('username')->setRequired( );
        $Form->get('password')->setRequired( );
        $Form->get('password_confirmation')->setRequired();
        
    }

	//Load the values if we are editing
	if( !$Form->isSubmitted() && $edit ){
		$Form->setValues( $Account );
	}
		
	//Check the post
	if( $Form->isValid() ){

        //Get the form values
		$values = $Form->getValues();
       
        //Only change the password if password confirmation is set
        if( !empty($values['password_confirmation']) ){
            $Account->password_salt = Auth::salt();
            $Account->password_hash = Auth::hash( $values['password_confirmation'] , $Account->password_salt );
        }

                
		//Save the Account
		$Account->sync( $values );
		$Account->save();

		//Show the flash and Redirect
        $Flash->success( t("Sucessfully edited account <a href='/admin/accounts/edit/?id=%{Account.id}'>%{Account.first_name} %{Account.last_name} (%{Account.username})</a>" , $Account ) , "/admin/accounts/" );		
		
	}
	
	Action::set( 'edit' , $edit );
	
	