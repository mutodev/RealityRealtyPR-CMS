<?php

//Ivan Zavala
$gerente = AccountTable::retrieveById(10010);

Action::set( 'gerente', $gerente );
Action::set( 'top_id', 'know-us-img' );
Action::set( 'page_title', 'PR TAX INCENTIVES' );
Action::set( 'sub_menu', array(
    array(
        'href' => '/incentives/act20',
        'label' => 'Act 20',
    ),
    array(
        'href' => '/incentives/act22',
        'label' => 'Act 22',
    ),
    array(
        'href' => '/incentives/act273',
        'label' => 'Act 273',
        'active' => true
    ),
));