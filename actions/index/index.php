<?php

//Set values
Action::set( 'top_id', array('home-img','home2-img','home3-img','home4-img','home5-img') );

$sent = false;

if (isset($_POST['email'])) {
    $Lead = new Lead();
    $Lead->first_name = $_POST['name'];
    $Lead->email = $_POST['email'];
    $Lead->type = 'NEWS';
    $Lead->save();
    $sent = true;
}

$specializedProperties = [
    'commercial' => [
        'name' => t('translation1021'),
        'description' => t('translation1073'),
    ],
    'reposubasta' => [
        'name' => t('translation1022'),
        'description' => t('translation1074'),
        'url' => 'http://reposubasta.com'
    ],
    'luxe' => [
        'name' => t('translation1023'),
        'description' => t('translation1075'),
    ],
    'newdevelopments' => [
        'name' => t('translation1024'),
        'description' => t('translation1076'),
    ]
];

$Properties = PropertyTable::getFeaturedProperties();

Action::set(compact('specializedProperties', 'Properties', 'sent'));
