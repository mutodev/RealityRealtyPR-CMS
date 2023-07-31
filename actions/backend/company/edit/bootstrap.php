<?php

$breadcrumb[] = array(
	'label' => t('Edit Company'),
	'url'   => url('backend.company.edit'),
	'icon'  => 'building'
);

function dateTimezonesList() {
    $values = DateTimeZone::listIdentifiers(DateTimeZone::AMERICA);

    return array_combine($values, $values);
}

