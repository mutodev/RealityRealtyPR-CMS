<?php

    Action::setLayout( 'login' );
    
    $code    = get('code');
    $Form    = helper('Form');
    $Request = helper('Request');
        
          
    if( $Request->isPost() && $Form->isValid() ){

        //Find the user associated with that code
        $q = Doctrine::getTable('Account')->createQuery();
        $q->select("Account.id , Account.username");
        $q->where("activation_code = ?" , $code );
        $Account = $q->fetchOne();
        
        //Check that the account actually exists
        if( !$Account ){
            redirect( '/accounts/password/' );
        }
                
        //Check that the two passwords match
        $password     = $Form->get('password')->getValue();
        $confirmation = $Form->get('confirm_password')->getValue();
        
        if( $password != $confirmation ){
            $Form->get('confirm_password')->setError( t('Password and conformation don\'t match') );
        }else{
        
            //Update the account
            $Account->salt            = Auth::salt();
            $Account->password_hash   = Auth::hash( $password , $Account->salt );
            $Account->activation_code = null;
            $Account->save();
                        
            //Login User
            Auth::login( $Account->username , $password );
            
            //Redirect to the home
            redirect( '/backend' );
            
        }
        

    
    }
    
    
    
