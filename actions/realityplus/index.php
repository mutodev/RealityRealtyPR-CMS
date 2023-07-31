<?php

vendor('SimplePie');

//Reinaldo Torres
$gerente = AccountTable::retrieveById(10007);
$gerente = current($gerente);

$feed = new SimplePie();
$feed->set_feed_url('http://www.realityrealtypr.com/blog/category/educativo/feed/');
$feed->set_cache_duration(3600);
$feed->init();
$feed->handle_content_type();
$feeds = $feed->get_items(0, 2);

Action::set( 'gerente', $gerente );
Action::set( 'feeds', $feeds);
Action::set( 'top_id', 'know-us-img' );
Action::set( 'page_title', 'NOSOTROS' );
Action::set( 'sub_menu', array(
    array(
        'href' => '/corredores',
        'label' => 'NUESTRO EQUIPO',
    ),
    /*
    array(
        'href' => '/testimonios',
        'label' => 'TESTIMONIOS'
    ),
    */
    array(
        'href' => '/realityplus',
        'label' => 'REALITY+ PROGRAM',
        'active' => true,
    ),
    array(
        'href' => '/blog/category/realityway/',
        'label' => '“REALITY WAY”'
    ),
));