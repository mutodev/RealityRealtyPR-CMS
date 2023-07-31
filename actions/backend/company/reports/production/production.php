<?php

$Search = helper('Search');
$filters = $Search->getSearch();

$report = [
	'listings' => [
		'title' => t('Listings'),
		'date_field' => 'start_at',
	],
	'options' => [
		'title' => t('Options'),
		'date_field' => 'option_start_at',
	],
	'closings' => [
		'title' => t('Closings'),
		'date_field' => 'sale_at',
	],
];

$yearsDiff = 2;

$summary = [];
$data = [];
$labels = [];
$chartQuantity = [];
foreach ($report as $name => $config) {
    $dateFrom =  $filters['date_from'];
    $dateTo =  $filters['date_to'];

    $dateFromDt = new DateTime($dateFrom);
    $dateToDt = new DateTime($dateTo);

    $dql = [];
    $dql[] = "(c.{$config['date_field']} >= ? AND c.{$config['date_field']} <= ?)";
    $params = [];
    $params[] = $filters['date_from'];
    $params[] = $filters['date_to'];

    foreach(range(1, $yearsDiff) as $inc) {
        $dql[] = "(c.{$config['date_field']} >= ? AND c.{$config['date_field']} <= ?)";

        $dtf = clone $dateFromDt;
        $dtt = clone $dateToDt;

        $params[] = $dtf->modify("+{$inc} year")->format('Y-m-d H:i:s');
        $params[] = $dtt->modify("+{$inc} year")->format('Y-m-d H:i:s');

        $dql[] = "(c.{$config['date_field']} >= ? AND c.{$config['date_field']} <= ?)";

        $dtf = clone $dateFromDt;
        $dtt = clone $dateToDt;

        $params[] = $dtf->modify("-{$inc} year")->format('Y-m-d H:i:s');
        $params[] = $dtt->modify("-{$inc} year")->format('Y-m-d H:i:s');
    }

	$Query = new Doctrine_Query();
	$Query->from('Contract c');
	$Query->leftJoin('c.Property p');
	//$Query->andWhere('('.implode(' OR ', $dql).')', $params);
	$Query->andWhere('p.production_unit_id IS NOT NULL');
	//$Query->andWhere('p.for_sale = 1');

    if ($filters['production_unit_id']) {
        $Query->andWhere("p.production_unit_id = ?", $filters['production_unit_id']);
    }

    $Query->orderBy("c.{$config['date_field']} ASC");

//    echo $Query->getSqlQuery();
//    prd($Query->getDql(), $params);

	$Contracts = $Query->execute();

	foreach ($Contracts as $Contract) {

	    //pr($Contract->Property[0]->id, $Contract->Property[0]->ProductionUnit->name);

		if ($name === 'listings') {
		    $year = date('Y', strtotime($Contract->start_at));

            $data[$year][$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['label'] = $Contract->Property[0]->ProductionUnit->name;
            $data[$year][$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['volume'] += $Contract->Property[0]->sale_price;
		} else if ($name === 'closings' && $Contract->sale_at) {
            $year = date('Y', strtotime($Contract->sale_at));

            $data[$year][$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['label'] = $Contract->Property[0]->ProductionUnit->name;
            $data[$year][$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['volume'] += $Contract->sale_agreed;
            $data[$year][$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['sale_commissions'] += $Contract->getSaleCommissionCalculated();
		} else if ($name === 'options' && $Contract->option_start_at) {
            $year = date('Y', strtotime($Contract->option_start_at));

            $data[$year][$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['label'] = $Contract->Property[0]->ProductionUnit->name;
            $data[$year][$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['volume'] += $Contract->option_deposit;
		}

        $data[$year][$name]['totalsByProductionUnit'][$Contract->Property[0]->ProductionUnit->name]['quantity']++;
	}
}

foreach ($data as $year => $g) {
    //$data[$year][$name]['byProductionUnit'] = array_values((array)@$data[$year][$name]['byProductionUnit']);
    $data[$year][$name]['totalsByProductionUnit'] = array_values((array)@$data[$year][$name]['totalsByProductionUnit']);
}

$charts = [];
$summary = [];
foreach ($data as $year => $typeData) {
    foreach ($typeData as $name => $moreData) {
        $charts[$name]['volume'][$year]['year'] = $year;
        $charts[$name]['quantity'][$year]['year'] = $year;

        if ($name === 'closings') {
            $charts[$name]['sale_commissions'][$year]['year'] = $year;
        }

        foreach ($moreData['totalsByProductionUnit'] as $d) {
            $labels[] = $d['label'];
            $charts[$name]['volume'][$year][$d['label']] = $d['volume'];
            $charts[$name]['quantity'][$year][$d['label']] = $d['quantity'];

            $summary[$year][$name]['volume'] += $d['volume'];
            $summary[$year][$name]['quantity'] += $d['quantity'];

            if ($name === 'closings') {
                $charts[$name]['sale_commissions'][$year][$d['label']] = $d['sale_commissions'];

                $summary[$year][$name]['sale_commissions'] += $d['sale_commissions'];
            }
        }
    }
}

$labels = array_values(array_filter(array_unique($labels)));

Action::set(compact('breadcrumb', 'data', 'charts', 'labels', 'report', 'summary'));
