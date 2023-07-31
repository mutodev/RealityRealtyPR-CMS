<?php

$activeService = get('service');

$services = array(
    //'overview' => t('translation1063'),
    'reventa' => t('Reventa'),
    'short-sales' => t('Short Sales'),
    'relocalizacion' => t('translation1066'),
    'commercial' => t('translation1067'),
    'luxe' => t('translation1068'),
    'newdevelopments' => t('Proyectos Nuevos'),
    'auction' => t('translation1070'),
    'education' => t('translation1071'),
    'consultoria' => t('translation1072'),
);

Action::set(compact('services', 'activeService'));
