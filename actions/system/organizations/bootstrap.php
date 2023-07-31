<?php

$breadcrumb[] = array(
    'label' => 'Organizations',
    'url'   => url('system.organizations'),
    'icon'  => 'building'
);

function plan_name($Plan) {

    if ($Plan->id) {
        return $Plan->iso_country_code ? "{$Plan->iso_country_code} - {$Plan->name}" : $Plan->name;
    }

    return 'None';
}
