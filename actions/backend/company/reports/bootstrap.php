<?php

$breadcrumb[] = array(
	'label' => 'Reports',
	'url'   => url('backend.company.reports'),
	'icon'  => 'pie-chart'
);


function getMonths() {
    $months = [];

    for ($x = 1; $x <= 12; $x++)
    {
        $month = str_pad($x, 2, "0", STR_PAD_LEFT);

        $date = DateTime::createFromFormat('!m', $x);
        $monthName = $date->format('F');

        $months[$month] = $monthName;
    }

    return $months;
}

function getYears() {
    $years = [];

    $Query = new Doctrine_Query();
    $Query->from('Goal g');
    $Query->andWhere('g.production_unit_id IS NOT NULL');
    $Query->groupBy('g.year');
    $Query->orderBy('g.year DESC');
    $Goals = $Query->execute();

    foreach ($Goals as $Goal) {
        $years[$Goal->year] = $Goal->year;
    }

    return $years;
}
