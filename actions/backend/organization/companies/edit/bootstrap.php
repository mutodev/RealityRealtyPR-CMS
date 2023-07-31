<?php

Auth::requirePermission('organization:company.manage');

$breadcrumb[] = array(
	'label' => get('id') ? t('Edit Company') : t('New Company'),
	'icon'  => 'gear'
);

function dateTimezonesList() {
    $values = DateTimeZone::listIdentifiers(DateTimeZone::AMERICA);

    return array_combine($values, $values);
}