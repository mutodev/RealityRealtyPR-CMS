<?php

    if( !Configure::read( 'debug') )
        Router::fowardForbidden();

    //Set the layout
    Action::setLayout(false);

    $Menu = helper( 'Menu' , 'main' );
    $Menu->add( "home"  , array(
        "label" => t('Home')   ,
        "link"  => "/yammon/" ,
    ));

    $Menu->add( "db"  , array(
        "label" => t('Database') ,
        "link"  => "/yammon/database"
    ));

    $Menu->add( "model"  , array(
        "label" => t('Model') ,
        "link"  => "/yammon/model"
    ));

/*
    $Menu->add( "translate"  , array(
        "label" => t('Translate') ,
        "link"  => "/yammon/translate"
    ));
*/

    $Menu->add( "permissions"  , array(
        "label" => t('Permissions') ,
        "link"  => "/yammon/permission"
    ));
    