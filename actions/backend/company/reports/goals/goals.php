<?php

$Search = helper('Search');
$filters = $Search->getSearch();

$report = [
    'listings' => [
        'title' => t('Listings'),
        'date_field' => 'start_at',
    ],
    'closings' => [
        'title' => t('Closings'),
        'date_field' => 'sale_at',
    ],
    'options' => [
        'title' => t('Options'),
        'date_field' => 'option_start_at',
    ],
    'closings_earnings' => [
        'title' => t('Commissions'),
        'date_field' => 'sale_at',
    ],
];

$yearsDiff = 2;

$summary = [];
$data = [];
$labels = [];
$chartQuantity = [];
foreach ($report as $name => $config) {

    $monthFrom = $filters['month'] ? $filters['month'] : '01';
    $monthTo = $filters['month'] ? $filters['month'] : '12';

    $dql = [];
    $dql[] = "(c.{$config['date_field']} >= ? AND c.{$config['date_field']} <= ?)";
    $params = [];
    $params[] = "{$filters['year']}-{$monthFrom}-01 00:00:00";
    $params[] = "{$filters['year']}-{$monthTo}-31 03:59:00";

    $Query = new Doctrine_Query();
    $Query->from('Contract c');
    $Query->leftJoin('c.Property p');
    $Query->andWhere('(' . implode(' OR ', $dql) . ')', $params);
    $Query->andWhere('p.production_unit_id IS NOT NULL');

    if ($filters['production_unit_id']) {
        $Query->andWhere("p.production_unit_id = ?", $filters['production_unit_id']);
    }

    $Query->orderBy("c.{$config['date_field']} ASC");

    $Contracts = $Query->execute();

    foreach ($Contracts as $Contract) {
        if ($name === 'listings') {
            $data[$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['label'] = $Contract->Property[0]->ProductionUnit->name;
            $data[$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['price_average'] += $Contract->sale_price;
            $data[$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['commission_average'] += $Contract->sale_value;
            $data[$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['volume'] += $Contract->sale_price;
        } else if ($name === 'closings' && $Contract->sale_at) {
            $data[$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['label'] = $Contract->Property[0]->ProductionUnit->name;
            $data[$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['price_average'] += $Contract->sale_agreed;
            $data[$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['commission_average'] += $Contract->sale_value;
            $data[$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['volume'] += $Contract->sale_agreed;
        } else if ($name === 'closings_earnings' && $Contract->sale_at) {
            $data[$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['quantity'] += $Contract->getSaleCommissionCalculated();
            $data[$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['price_average'] += $Contract->sale_agreed;
            $data[$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['commission_average'] += $Contract->sale_value;
            $data[$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['volume'] += $Contract->sale_agreed;
        } else if ($name === 'options' && $Contract->option_start_at) {
            $data[$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['label'] = $Contract->Property[0]->ProductionUnit->name;
            $data[$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['price_average'] += $Contract->sale_agreed;
            $data[$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['commission_average'] += $Contract->sale_value;
            $data[$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['volume'] += $Contract->option_deposit;
        }

        if ($name != 'closings_earnings') {
            $data[$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['quantity']++;
        }
    }
}

foreach ($data as $name => $g) {
    $data[$name]['totalsByProductionUnit'] = array_values((array)@$data[$name]['totalsByProductionUnit']);
}

$charts = [];
$summary = [];
foreach ($data as $name => $moreData) {
    foreach ($moreData['totalsByProductionUnit'] as $d) {
        $summary[$name]['quantity'] += $d['quantity'];
        $summary[$name]['price_average'] += $d['price_average'];
        $summary[$name]['commission_average'] += $d['commission_average'];
        $summary[$name]['volume'] += $d['volume'];
    }
}

$Query = new Doctrine_Query();
$Query->from('Goal g');
$Query->andWhere('g.year = ?', date('Y'));
$Query->andWhere('g.production_unit_id IS NOT NULL');
$Goals = $Query->execute();

$goals = [];
foreach ($Goals as $Goal) {
    foreach (array_keys($report) as $name) {
        $goals[$name] += $filters['month'] ? ($Goal[$name] / 12) : $Goal[$name];

        if (in_array($name, ['listings', 'closings'])) {
            $goals["{$name}_volume"] += $filters['month'] ? $Goal["{$name}_volume"] / 12 : $Goal["{$name}_volume"];
        }
    }

    $goals['listings_price_average'] += $Goal->listings_price_average;
    $goals['listings_commission_average'] += $Goal->listings_commission_average;

    $goals['closings_price_average'] += $Goal->closings_price_average;
    $goals['closings_commission_average'] += $Goal->closings_commission_average;
}

$labels = array_values(array_filter(array_unique($labels)));

Action::set(compact('breadcrumb', 'data', 'charts', 'labels', 'report', 'summary', 'goals'));
