<?php

    Configure::write("layout" , "core" );

    $Menu = helper( 'Menu' , 'main' );
    $Menu->add( "home"  , array(
        "label" => t('Home')   ,
        "link"  => "developer" ,
    ));

    $Menu->add( "db"  , array(
        "label" => t('Database') ,
        "link"  => "developer.database"
    ));

    $Menu->add( "model"  , array(
        "label" => t('Model') ,
        "link"  => "developer.model"
    ));

    $Menu->add( "auth"  , array(
        "label" => t('Auth') ,
        "link"  => "developer.auth" ,
    ));
    