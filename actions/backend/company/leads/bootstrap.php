<?php

Auth::requirePermission('property.access');

$breadcrumb[] = array(
	'label' => 'Leads',
	'url'   => url('backend.company.leads'),
	'icon'  => 'exchange'
);

function renderLooking($Lead){

    $html = array();

    if ($Lead->property_id) {
    	$html[] = '#'.$Lead->Property->id.' - '.$Lead->Property->title;
    }

    foreach($Lead->Searches as $Search){
    	$search = array();

    	if ($Search->price_min) {
    		$search[] = '>= '.decorator('Dollars', $Search->price_min);
    	}

    	if ($Search->price_max) {
    		$search[] = '<= '.decorator('Dollars', $Search->price_max);
    	}

    	if ($Search->category_id) {
    		$search[] = $Search->Category->type.'/'.$Search->Category->name;
    	}

    	if ($Search->area_id) {
    		$search[] = $Search->Area->name_es;
    	}

    	$html[] = implode(' - ', $search);
    }

    return implode('<br />', $html);
}