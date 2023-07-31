<?php

$caguas = AccountTable::retrieveById(32366);

$metro = AccountTable::retrieveById(32651);

$santurce = AccountTable::retrieveById(30168);

$bayamon = AccountTable::retrieveById(32650);

//Set values
Action::set('caguas', $caguas);
Action::set('metro', $metro);
Action::set('santurce', $santurce);
Action::set('bayamon', $bayamon);
//Action::set( 'top_id', 'know-us-img' );
Action::set( 'page_title', t('translation118') );
Action::set( 'sub_menu', array(
    array(
        'href' => '/servicios/reventa',
        'label' => t('translation179'),
        'active' => true
    ),
    array(
        'href' => '/servicios/comercial',
        'label' => t('translation149'),
    ),
    array(
        'href' => '/servicios/reposeidas',
        'label' => t('translation180'),
    ),
    array(
        'href' => '/servicios/short-sales',
        'label' => 'SHORT SALES',
    ),
    array(
        'href' => '/servicios/relocalizacion',
        'label' => t('translation181'),
    ),
    array(
        'href' => '/servicios/reposubasta',
        'label' => t('translation182'),
    ),
    array(
        'href' => '/servicios/proyectos-nuevos',
        'label' => t('translation150'),
    ),
    array(
        'href' => '/servicios/consultoria',
        'label' => t('translation183'),
    ),
    array(
        'href' => '/servicios/franquicias',
        'label' => t('translation184'),
    ),
));
