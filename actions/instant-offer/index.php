<?php

$Form = helper('Form');
$Flash = helper('Flash');

if ($Form->isValid()) {
    $values = $Form->getValues();

    //Send the recovery mail
    $Mail = new Mail();
    $Mail->setFrom('noreply@realityrealtypr.com');
    $Mail->set('values'    , $values );
    $Mail->setHtmlFile( dirname( __FILE__ ) . DS . "email.phtml" );
    $Mail->addTo( 'eduardito58@gmail.com' );
    $Mail->addTo( 'zavalai@realityrealtypr.com' );
    $Mail->addTo( 'serranomil@realityrealtypr.com' );
    $Mail->setSubject( t('Instant Offer') );
    $Mail->send();

    $Offer = new InstantOffer();
    $Offer->syncAndSave($values);

    $Flash->success('Gracias, estaremos contactando a la mayor brevedad' , url('.') );
}
