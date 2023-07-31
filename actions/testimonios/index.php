<?php

//Set values
Action::set( 'top_id', 'know-us-img' );
Action::set( 'page_title', 'NOSOTROS' );
Action::set( 'sub_menu', array(
    array(
        'href' => '/corredores',
        'label' => 'NUESTRO EQUIPO',
    ),
    array(
        'href' => '/testimonios',
        'label' => 'TESTIMONIOS',
        'active' => true
    ),
    array(
        'href' => '#',
        'label' => 'AFILIADOS (REIRE)'
    ),
    array(
        'href' => '/blog',
        'label' => 'NOTICIAS'
    ),
));