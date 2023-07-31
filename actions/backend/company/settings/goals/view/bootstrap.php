<?php

$year = get('year', date('Y'));
$month = get('month', date('m'));

$month = str_pad($month, 2, '0', STR_PAD_LEFT);

$Query = new Doctrine_Query();
$Query->from('Goal g');

if ($accountId = get('account_id')) {
    $Query->andWhere('account_id = ?', $accountId);
}

if ($productionUnitId = get('production_unit_id')) {
    $Query->andWhere('production_unit_id = ?', $productionUnitId);
}

$GoalQuery = clone $Query;

$Query->andWhere('g.year = ?', $year);

$Goal = $Query->fetchOne();

$Goals = $GoalQuery->execute();

$breadcrumb[] = array(
	'label' => $Goal ? 'Goals for '.($Goal->account_id ? $Goal->Account->getFullName() : $Goal->ProductionUnit->name) : t('No goals set for '.$year),
	'url'   => url('backend.company.accounts.goals.view'),
	'icon'  => 'line-chart'
);

