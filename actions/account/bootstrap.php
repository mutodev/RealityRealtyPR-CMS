<?php

//Set Layout
Action::setLayout('account');

function register_account( $values  ){

	$Account = new Account();
	$Account->sync( $values );

	//Set new activation key and expiration
	do{
		$key = substr( sha1( Configure::read('auth.salt') . sha1( rand() ) ) , 0 , 8 );
		$matchAccount = Doctrine::getTable('Account')->findOneByActivationKey($key);
	}while( !empty( $matchAccount ) );

	$Account->activation_key            = $key;
	$Account->activation_key_expires_at = date( "Y-m-d H:i:s" , time() + (1 * 24 * 60 * 60) );

    //Set Username and Password
    $Account->staff_id = 1;
	$Account->username = $values['email'];
	$Account->setPassword( $values['password'] );
	$Account->save();

	//Create first office
	$Office = new Office();
	$Office->sync($values['Organization']);
	$Office->organization_id = $Account->organization_id;
	$Office->save();

	//Send Validation Email
    //send_validation( $Account );
}

function send_validation( $Account ){

	//Notification
	Notification_Account::activation(array(
		'id' => $Account->id
	));
}

function validate_account( $key ){

    //Get the Account
	$q = new Doctrine_Query();
	$q->from('Account');
	$q->andWhere( 'activation_key = ? '             , $key          );
	$q->andWhere( 'activation_key_expires_at > ? '  , date("Y-m-d") );
	$q->andWhere( 'active = ? ' 					, false 		);
    $Account = $q->fetchOne();

    //Check that the Account exists
	if( empty( $Account ) )
        return t('Invalid Validation Key');

    //Activate the Account
    $Account->activation_key 			= null;
    $Account->activation_key_expires_at = null;
    $Account->active 					= true;
    $Account->save();

    //Add Account to Free Plan
    $Role = Doctrine::getTable('Role')->find('organization.admin');
    Auth::addAccountToRole( $Account , $Role );

    //Login
    Auth::forceLogin( $Account );

    return true;

}
