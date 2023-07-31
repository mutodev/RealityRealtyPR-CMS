<?php

$Request = helper("Request");

$id     = post('id') ? post('id') : get('id');
$errors = array();
$values = array();

Action::setLayout(false);

//Property
$Property = PropertyTable::retrieveById($id);

if( !$Property || !$Property->active ){
    exit;
}

if( $Request->isPost() ){

    $values['myname']  = post('myname');
    $values['myemail'] = post('myemail');
    $values['name']    = post('name');
    $values['email']   = post('email');

    //Validation
    if ( !preg_match("/^[^0-9][A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/", $values['myemail']) ) {
        $errors['myemail'] = t('translation310');
    }
    if ( !preg_match("/^[^0-9][A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/", $values['email']) ) {
        $errors['email'] = t('translation310');
    }
    if ( strlen($values['myname']) < 6 ) {
        $errors['myname'] = t('translation312');
    }
    if ( strlen($values['name']) < 6 ) {
        $errors['name'] = t('translation312');
    }

    //Valid
    if ( empty( $errors ) ) {

        //Get The Information out of the config
        $mail_from        = ListMax::config("mail_from");
        $mail_from_name   = ListMax::config("mail_from_name");
        $url              = (!empty( $_SERVER['HTTPS'] ) ? "https://" : "http://" ) . $_SERVER['HTTP_HOST'] . url_property( $Property );

        //Create Subject
        $mail_subject = $values['myname']." ".t('translation315');

        //Set up the mailer instance
        $mailer = new Mailer();
        $mailer->setSubject( $mail_subject );
        $mailer->addTo($values['email']);

        if( !empty( $mail_from ) ){
            prd($mail_from , $mail_from_name);
           $mailer->setFrom( $mail_from , $mail_from_name );
           $mailer->setReturnPath( $mail_from );
        }

        $mailer->set( array( 'Property'     => $Property ,
                             'url'          => $url
        ));

        $mailer->send('emailproperty');
    }

}

//Set values
Action::set( 'Property', $Property );
Action::set( 'id'      , $id );
Action::set( 'errors'  , $errors );
Action::set( 'values'  , $values );
Action::set( 'isDone'    , ($Request->isPost() && empty( $errors )) );