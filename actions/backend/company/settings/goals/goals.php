<?php

$Table = helper('Table');

$Query = $Table->getSource();

if ($accountId) {
    $Query->andWhere('account_id = ?', $accountId);
}

if ($productionUnitId) {
    $Query->andWhere('production_unit_id = ?', $productionUnitId);
}

$Query->orderBy('year DESC');
$Table->setOption('source', $Query);

Action::set(compact('breadcrumb', 'accountId', 'productionUnitId'));
