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
	'label' => t('Offers'),
	'url'   => url('backend.company.properties'),
	'icon'  => 'tags'
);

function renderSalerice($Contract){

    $html = array();
    $html[] = decorator('dollars', $Contract->sale_price);

    return implode('', $html);
}

function renderClients($Contract){

    $html = array();
    foreach($Contract->Clients as $Client){
        $html[] = $Client->first_name.' '.$Client->last_name;
    }

    return implode('<br />', $html);
}
