<?php

Auth::requirePermission('organization:manage');

$breadcrumb[] = array(
	'label' => t('Edit Organization'),
	'url'   => url('backend.organization.information.edit'),
	'icon'  => 'building'
);

function dateTimezonesList() {
    $values = DateTimeZone::listIdentifiers(DateTimeZone::AMERICA);

    return array_combine($values, $values);
}

