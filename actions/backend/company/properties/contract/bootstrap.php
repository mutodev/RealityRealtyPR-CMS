<?php

//Auth::requirePermission('campaign.access');

$propertyId = get('property_id');

$Property = Doctrine::getTable('Property')->find($propertyId);

if($Property){

    $breadcrumb[] = array(
        'label' => '#'.$Property->id .' - '.$Property->title,
        'url'   => url('backend.company.properties.view?id='.$Property->id),
        'icon'  => 'home'
    );
}

$breadcrumb[] = array(
	'label' => 'Contract',
	'url'   => url('backend.company.properties'),
	'icon'  => 'file-text-o'
);

function renderSalerice($Contract){

    $html = array();
    $html[] = decorator('dollars', $Contract->sale_price);

    return implode('', $html);
}

function renderClient($Contract){

    $html = array();
    $html[] = $Contract->Client->first_name.' '.$Contract->Client->last_name;

    return implode('<br />', $html);
}
