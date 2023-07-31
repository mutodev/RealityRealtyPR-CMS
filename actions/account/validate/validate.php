<?php

	$FormValidate  = helper('Form' , 'validate' , 'validate.form.yml' );
	$FormResend    = helper('Form' , 'resend'   , 'resend.form.yml'   );
	$Flash = helper('Flash');

	$key     = get('key');
    $Account = null;
    
    //Set the Layout to publish
    $distribution = Session::read('distribution');
    if( !empty( $distribution ) )
        Action::setLayout('publish');
    
    //Enter the key automatically
	if( !$FormValidate->isSubmitted() && !empty( $key ) )
		$FormValidate->get('key')->setValue($key);

    //Validate Account
	if( $FormValidate->isValid() )
	{
	
		$key = $FormValidate->get('key')->getValue();
        $success = validate_account( $key );
        
        if( $success === true ){
    	    redirect( url('backend') );         
        }else{
			$FormValidate->get('key')->setError( $success );               
        }
	
        $Flash->success( t('Your account was successfully validated') );		
	
    }		
    
    //Resend Email
	if( $FormResend->isSubmitted() ){

		//Get email
		$email = $FormResend->get('email')->getValue();

		//Make sure account exists
		$q = new Doctrine_Query();
		$q->from('Account');
		$q->andWhere('active = ?' , false);
		$q->andWhere('email = ?'  , $email);
		$Account = $q->fetchOne();

		//Make sure account exists
		if( empty( $Account ) )
			$FormResend->get('email')->setError( t("We don't have records for the email provided") );

	}

	if( $FormResend->isValid() ){

		//Send Confirmation Email
		send_validation( $Account );
        $Flash->success( t('We sent you the email again, don\'t forget to check your spam folder') );	

	}        

	Action::set(compact('FormValidate', 'FormResend'));