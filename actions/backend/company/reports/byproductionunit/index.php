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
            $data[$Contract->Property[0]->ProductionUnit->name][$name]['label'] = $Contract->Property[0]->ProductionUnit->name;
            $data[$Contract->Property[0]->ProductionUnit->name][$name]['volume'] += $Contract->Property[0]->sale_price;
            $data[$Contract->Property[0]->ProductionUnit->name][$name]['agents'][$Contract->Primary->getFullName()]['volume'] += $Contract->Property[0]->sale_price;

            $data[$Contract->Property[0]->ProductionUnit->name][$name]['price_average'] += $Contract->sale_price;
            $data[$Contract->Property[0]->ProductionUnit->name][$name]['commission_average'] += $Contract->sale_value;

            if ($Contract->secondary_account_id) {
                $data[$Contract->Property[0]->ProductionUnit->name][$name]['agents'][$Contract->Secondary->getFullName()]['volume'] += $Contract->Property[0]->sale_price;
            }
        } else if ($name === 'closings' && $Contract->sale_at) {
            $data[$Contract->Property[0]->ProductionUnit->name][$name]['label'] = $Contract->Property[0]->ProductionUnit->name;
            $data[$Contract->Property[0]->ProductionUnit->name][$name]['volume'] += $Contract->sale_agreed;
            $data[$Contract->Property[0]->ProductionUnit->name][$name]['agents'][$Contract->Primary->getFullName()]['volume'] += $Contract->sale_agreed;

            $data[$Contract->Property[0]->ProductionUnit->name][$name]['price_average'] += $Contract->sale_agreed;
            $data[$Contract->Property[0]->ProductionUnit->name][$name]['commission_average'] += $Contract->sale_value;

            if ($Contract->secondary_account_id) {
                $data[$Contract->Property[0]->ProductionUnit->name][$name]['agents'][$Contract->Secondary->getFullName()]['volume'] += $Contract->sale_agreed;
            }
        } else if ($name === 'closings_earnings' && $Contract->sale_at) {
            $data[$Contract->Property[0]->ProductionUnit->name][$name]['quantity'] += $Contract->getSaleCommissionCalculated();
            $data[$Contract->Property[0]->ProductionUnit->name][$name]['label'] = $Contract->Property[0]->ProductionUnit->name;
            $data[$Contract->Property[0]->ProductionUnit->name][$name]['volume'] += $Contract->sale_agreed;
            $data[$Contract->Property[0]->ProductionUnit->name][$name]['agents'][$Contract->Primary->getFullName()]['volume'] += $Contract->sale_agreed;

            $data[$Contract->Property[0]->ProductionUnit->name][$name]['price_average'] += $Contract->sale_agreed;
            $data[$Contract->Property[0]->ProductionUnit->name][$name]['commission_average'] += $Contract->sale_value;

            if ($Contract->secondary_account_id) {
                $data[$Contract->Property[0]->ProductionUnit->name][$name]['agents'][$Contract->Secondary->getFullName()]['volume'] += $Contract->sale_agreed;
            }
        } else if ($name === 'options' && $Contract->option_start_at) {
            $data[$Contract->Property[0]->ProductionUnit->name][$name]['label'] = $Contract->Property[0]->ProductionUnit->name;
            $data[$Contract->Property[0]->ProductionUnit->name][$name]['volume'] += $Contract->option_deposit;
            $data[$Contract->Property[0]->ProductionUnit->name][$name]['agents'][$Contract->Primary->getFullName()]['volume'] += $Contract->option_deposit;

            $data[$Contract->Property[0]->ProductionUnit->name][$name]['price_average'] += $Contract->sale_agreed;
            $data[$Contract->Property[0]->ProductionUnit->name][$name]['commission_average'] += $Contract->sale_value;

            if ($Contract->secondary_account_id) {
                $data[$Contract->Property[0]->ProductionUnit->name][$name]['agents'][$Contract->Secondary->getFullName()]['volume'] += $Contract->option_deposit;
            }
        }

        if ($name != 'closings_earnings') {
            $data[$Contract->Property[0]->ProductionUnit->name][$name]['quantity']++;
            $data[$Contract->Property[0]->ProductionUnit->name][$name]['agents'][$Contract->Primary->getFullName()]['quantity']++;

            if ($Contract->secondary_account_id) {
                $data[$Contract->Property[0]->ProductionUnit->name][$name]['agents'][$Contract->Secondary->getFullName()]['quantity']++;
            }
        }
    }
}

$Query = new Doctrine_Query();
$Query->from('Goal g');
$Query->leftJoin('g.ProductionUnit u');
$Query->andWhere('g.year = ?', date('Y'));
$Query->andWhere('g.production_unit_id IS NOT NULL');
$Goals = $Query->execute();

$goals = [];
foreach ($Goals as $Goal) {
    foreach (array_keys($report) as $name) {
        $goals[$Goal->ProductionUnit->name][$name] += $filters['month'] ? ($Goal[$name] / 12) : $Goal[$name];

        if (in_array($name, ['listings', 'closings'])) {
            $goals[$Goal->ProductionUnit->name]["{$name}_volume"] += $filters['month'] ? $Goal["{$name}_volume"] / 12 : $Goal["{$name}_volume"];
        }
    }

    $goals[$Goal->ProductionUnit->name]['listings_price_average'] += $Goal->listings_price_average;
    $goals[$Goal->ProductionUnit->name]['listings_commission_average'] += $Goal->listings_commission_average;

    $goals[$Goal->ProductionUnit->name]['closings_price_average'] += $Goal->closings_price_average;
    $goals[$Goal->ProductionUnit->name]['closings_commission_average'] += $Goal->closings_commission_average;
}

//prd($goals, $data);

Action::set(compact('breadcrumb', 'data', 'charts', 'labels', 'report', 'summary', 'goals'));
