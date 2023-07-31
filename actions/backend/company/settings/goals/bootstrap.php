<?php

if ($accountId = get('account_id')) {
    $Account = Doctrine::getTable('Account')->find($accountId);

    $breadcrumb[] = array(
        'label' => t('Accounts'),
        'url'   => url('backend.company.accounts'),
        'icon'  => 'line-chart'
    );

    $breadcrumb[] = array(
        'label' => $Account->getFullName(),
        'url'   => url('backend.company.settings.goals?account_id='.$accountId),
        'icon'  => 'line-chart'
    );
}

if ($productionUnitId = get('production_unit_id')) {
    $ProductionUnit = Doctrine::getTable('ProductionUnit')->find($productionUnitId);

    $breadcrumb[] = array(
        'label' => t('Production Units'),
        'url'   => url('backend.company.settings.production'),
        'icon'  => 'line-chart'
    );

    $breadcrumb[] = array(
        'label' => $ProductionUnit->name,
        'url'   => url('backend.company.settings.goals?production_unit_id='.$productionUnitId),
        'icon'  => 'line-chart'
    );
}


Action::set(compact('breadcrumb'));
