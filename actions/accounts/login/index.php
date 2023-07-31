<?php

    Action::setLayout( 'login' );

    $Request = helper('Request');
    $Form    = helper('Form');
    $Flash   = helper('Flash');

    $url     = get("r" , "/admin");

   
    if( $Request->isPost() ){

        $username    = $Form->getValue('username');
        $password    = $Form->getValue('password');
        
        if( Auth::login( $username , $password  ) ){
            redirect( $url );
        }else{
            $Flash->error( Auth::getLoginError() );
        }

    }

    

