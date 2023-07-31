<?php

    Action::setLayout( 'login' );
    
    $Request = helper('Request');
    $Form    = helper('Form');
    
    //Get Variables
    $code = get('code');
    $sent = false;
        
    //Send an email
    if( $Request->isPost() && $Form->isValid() ){
    
        //Check that the email exists
        $email   = $Form->getValue('email');
          
        $q = Doctrine::getTable('Account')->createQuery();
        $q->select("Account.username");
        $q->where( "Account.email LIKE ?" , $email );
        $Account = $q->fetchOne();

        //Check if the account exists
        if( empty( $Account ) ){
            $Form->get('email')->setError( t("This email is not registered") );
        }else{

            //Create new Activation code
            $Account->activation_code = Auth::salt();
            $Account->save();

            //Send the recovery mail
            $Mail = new Mail();
            $Mail->setHtmlFile( dirname( __FILE__ ) . DS . "index.mhtml" );
            $Mail->setTextFile( dirname( __FILE__ ) . DS . "index.mtxt" );
            $Mail->addTo( $email );
            $Mail->setSubject( t('Password Recovery') );
            $Mail->set('email'    , $email );
            $Mail->set('username' , $Account->username );
            $Mail->set('code'     , $Account->activation_code );
            $Mail->send();

        }
    
        $sent = true;        
            
    }

    Action::set( 'sent' , $sent );