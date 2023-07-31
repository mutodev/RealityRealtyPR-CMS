<?php

#----- Submit Contact Form -----------------------------------------------------

error_reporting( E_ALL );
ini_set('display_errors' , 1 );

if ( $_SERVER['HTTP_X_AJAX_REQUEST'] == 'CONTACT_FORM' ) {

    $from          = strtolower( post('from') );
    $email         = post('var3');
    $name          = post('var1');
    $phone         = preg_replace( "/[^0-9]/" , "" , post('var2'));
    $msg           = post('var4');
    $property_id   = post('property_id');
    $url           = post('url');
    $office        = post('office');
    $broker_id     = post('broker_id');

    $broker = false;

    if($broker_id){
        $broker = AccountTable::retrieveById($broker_id);
    }

    // Validation
    $errors = array();

    if ( !preg_match("/^[^0-9][A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/", $email) ) {
        $errors[] = t('translation310');
    }
    if ( strlen($msg) < 6 ) {
        $errors[] = t('translation311');
    }
    if ( $from == 'main' || $from == 'property' ) {
        if ( strlen($name) < 2 ) {
            $errors[] = t('translation312');
        }
//        if ( strlen($phone) < 10 ) {
//            $errors[] = t('translation313');
//        }
    }

    if($office == 'none'){
        $errors[] = t('translation314');
    }

    // Return Errors it the is any
    if ( !empty($errors) ) {
        exit( json_encode( $errors ) );
    }

    //Property
    if ( $from == 'property' && $property_id && is_numeric($property_id) ) {
        $Property = PropertyTable::retrieveById( $property_id );


        if ( !$Property ) exit;

        //
        $mailer = new Mail();
        $mailer->addTo( $broker ? $broker->email : $Property->Agent->email );
        $mailer->setSubject( "Property #".$property_id.", Contact Form RealityRealtyPR.com" );
        $mailer->setFrom( "noreply@realityrealtypr.com" );
//        $mailer->setReturnPath( "noreply@realityrealtypr.com" );
        if( !empty( $email ) ){
             $mailer->setReplyTo( $email );
        }

        ob_start();
        include("property.phtml");
        $html = ob_get_clean();

        //$mailer->setMode('text');
        $mailer->setHtml($html );
        $mailer->send();

        $Lead = new Lead();
        $Lead->syncAndSave(array(
            'property_id' => $Property->id,
            'first_name'  => $name,
            'email'       => $email ,
            'phone'       => $phone ,
            'notes'       => $msg,
            'type'        => 'Buyer',
            'source_id'   => 1,
            'account_id'  => ($broker ? $broker->id : $Property->account_id),
        ));

    }
    else {

        $office   = post('office');

        //Create Subject
        $site         = $_SERVER['HTTP_HOST'];
        $mail_subject = "Formulario de Contacto desde $site";

        //Set up the mailer instance
        $mailer = new Mail();

        if($broker){
            //$mailer->addTo( 'eduardito58@gmail.com' );
            $mailer->addTo( $broker->email );
        }

        //$mailer->addTo( 'eduardito58@gmail.com' );

        $mailer->setSubject( $mail_subject );

        $mailer->setFrom( "noreply@realityrealtypr.com"  );

        if( !empty( $email ) ){
             $mailer->setReplyTo( $email );
        }

        $mailer->set( array( 'name'         => $name ,
                             'email'        => $email ,
                             'phone'        => $phone ,
                             'msg'          => $msg
        ));

        $Lead = new Lead();
        $Lead->syncAndSave(array(
            'first_name'  => $name,
            'email'       => $email ,
            'phone'       => $phone ,
            'notes'       => $msg,
            'source_id'   => 1,
            'account_id'  => ($broker ? $broker->id : null),
        ));

		//Aqui descanza un Panda muerto el 9 de agosto de 2011 (RIP)
		$content = "<p style='font-weight: bold;'>".t("Formulario enviado:")."</p>
					<ul>
						<li>". t('Nombre: ').$name ."</li>
						<li>". t('Email: ').$email ."</li>
						<li>". t('Tel.: ').$phone ."</li>
						<li>
						    ". t('Mensaje: ') ." <br /><br />
					    	". nl2br( strip_tags($msg) ) ."
						</li>

					</ul>";

		$mailer->setHtml($content);
    }

    exit('done');
}

exit();
