<?php

//Auth::requirePermission('campaign.access');

$breadcrumb[] = array(
	'label' => 'Properties',
	'url'   => url('backend.company.properties'),
	'icon'  => 'home'
);

function viewLink( $id ){
    return url('.sources?campaign_id='. $id);
}

function renderPrice($Property){

    $html = array();

    if($Property->for_sale){
        $html[] = '<small>sale</small> '.decorator('dollars', $Property->sale_price);
    }

    if($Property->for_rent){
        $html[] = '<small>rent</small> '.decorator('dollars', $Property->rent_price);
    }

    if($Property->is_short_sale){
        $html[] = '<small>short sale</small>';
    }

    if($Property->is_repossessed){
        $html[] = '<small>REPO</small>';
    }

    return implode('<br />', $html);
}

function renderDescription($Property){

    $html = array();
    $html[] = '<strong>#'.$Property->id.'</strong> '.$Property->title.', '.$Property->Area->name_es;
    $html[] = '<br /><strong>'.$Property->Category->type.' - '.$Property->Category->name.'</strong>';
    $html[] = '<br />'.$Property->internal_number;

    return implode('', $html);
}

function renderArea($Property){

    return $Property->Area->name_es;
}

function renderDetails($Property) {
    $html = array();
    if ($Property->bathrooms) {
        $html[] = '<strong>Baths</strong> '.(float)$Property->bathrooms;
    }

    if ($Property->rooms) {
        $html[] = '<strong>Bedrooms</strong> '.$Property->rooms;
    }

    if ($Property->sqf) {
        $html[] = '<strong>SQF</strong> '.number_format($Property->sqf);
    }

    if ($Property->sqm) {
        $html[] = '<strong>SQM</strong> '.number_format($Property->sqm);
    }

    return implode('<br />', $html);
}
