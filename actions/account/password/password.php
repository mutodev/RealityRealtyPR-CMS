<?php

	$Form  = helper('Form');
	$Flash = helper('Flash');

	if( $Form->isSubmitted() ){
		$email   = $Form->get('email')->getValue();

		//Check if it is a valid email
	    $q = new Doctrine_Query();
    	$q->from('Account');
	    $q->andWhere('email LIKE ?' , $email );
	    $Account = $q->fetchOne();

		if( empty( $Account ) )
			$Form->get('email')->setError( t('No account was found for that email') );
	}

	if( $Form->isValid() ){

		$email   = $Form->get('email')->getValue();

        //Find the account
	    $q = new Doctrine_Query();
    	$q->from('Account');
	    $q->andWhere('email LIKE ?' , $email );
	    $Account = $q->fetchOne();

        //Get the password key
    	if( $Account->password_key && $Account->password_key_expires_at > date('Y-m-d H:i:s') ){
    	    $key = $Account->password_key;
        }else{

            do{
                $key = substr( sha1( Configure::read('auth.salt') . sha1( rand() ) ) , 0 , 8 );
                $matchAccount = Doctrine::getTable('Account')->findOneByPasswordKey($key);
            }while( !empty( $matchAccount ) );

        }

		$Account->password_key            = $key;
		$Account->password_key_expires_at = date( "Y-m-d H:i:s" , time() + (1 * 24 * 60 * 60) );
		$Account->save();

        //Send the recovery mail
        $Mail = new Mail();
        $Mail->setFrom('noreply@realityrealtypr.com');
        $Mail->addTo( $email );
        $Mail->setSubject( t('Password Recovery') );
        $Mail->set('key', $Account->password_key );
        $Mail->set('name', $Account->first_name);
        $Mail->setHtmlFile('password-email.phtml');
        $Mail->send();

        $Flash->success( t('We sent you an email with further instructions, don\'t forget to check your spam folder.') );

	}
