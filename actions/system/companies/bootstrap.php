<?php

$breadcrumb[] = array(
    'label' => 'Customers',
    'url'   => url('system.companies'),
    'icon'  => 'building'
);

function plan_name($Plan) {

    if ($Plan->id) {
        return $Plan->iso_country_code ? "{$Plan->iso_country_code} - {$Plan->name}" : $Plan->name;
    }

    return 'None';
}
