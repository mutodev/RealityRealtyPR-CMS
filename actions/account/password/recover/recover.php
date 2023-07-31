<?php

	$Form  = helper('Form');
	$Flash = helper('Flash');

	//Get Password key
	$key = get('key');

	//Find Account
	$q = new Doctrine_Query();
	$q->from('Account');
	$q->andWhere('password_key = ?' , $key );
	$q->andWhere('password_key_expires_at > ?' , date('Y-m-d H:i:s') );
	$Account = $q->fetchOne();

	if( empty( $Account ) ){
	    $Flash->error('The password recovery timed out. Please request a new email' , '..' );
    }
    
	if( $Form->isValid() ){

		$values = $Form->getValues();
        $Account->setPassword( $values['password'] );
        $Account->password_key = null;
        $Account->password_key_expires_at = null;        
        $Account->save();

        Auth::forceLogin( $Account );
        $Flash->success('Successfully changed your password' , url('index') );        

	}

	Action::set('Account' , $Account);