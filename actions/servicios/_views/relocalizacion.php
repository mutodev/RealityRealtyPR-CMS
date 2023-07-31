<?php

//Euri
$gerente = AccountTable::retrieveById(32677);

Action::set( 'gerente', $gerente );
Action::set( 'page_title', t('translation118') );
Action::set( 'sub_menu', array(
    array(
        'href' => '/servicios/reventa',
        'label' => t('translation179'),
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
        'active' => true
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
