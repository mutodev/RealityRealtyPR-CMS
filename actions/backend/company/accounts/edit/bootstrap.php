<?php

Auth::requirePermission('account.manage');

$breadcrumb[] = array(
	'label' => get('id') ? t('Edit Account') : t('New Account'),
	'icon'  => 'user'
);

function selectWorkingHours() {

    $times = array();

    for ($i = 0; $i < 86400; $i += 1800) {

        $date = new DateTime();
        $date->setTimestamp($i)->setTimezone(new DateTimeZone('UTC'));

        $times[$date->format('H:i:00')] = $date->format('h:i A');
    }

    //Add 23:59:59
    $times['23:59:59'] = '11:59 PM';

    return $times;
}

function validSourcesQuery($element) {

    $validRolesIds = array();
    $validRolesIds[] = 'company.agent';
    $validRolesIds[] = 'company.assistant';
    $validRolesIds[] = 'company.manager';

    $Query = new Doctrine_query();
    $Query->from('Role');
    $Query->andWhereIn('id', $validRolesIds);

    return $Query;
}
