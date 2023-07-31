<?php

//Auth::requirePermission('campaign.access');

$breadcrumb[] = array(
	'label' => 'Properties Stats',
	'url'   => url('backend.company.reports.stats'),
	'icon'  => 'graph'
);

function renderPrice($Property){

    $html = array();

    $Property = $Property->Property;

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

    $Property = $Property['Property'];

    $html = array();
    $html[] = '<strong>#'.$Property['id'].'</strong> '.$Property['title'].', '.$Property['Area']['name_es'];
    $html[] = '<br /><strong>'.$Property['Category']['type'].' - '.$Property['Category']['name'].'</strong>';
    $html[] = '<br />'.$Property['internal_number'];

    return implode('', $html);
}

function renderStat($Property) {
//prd($Property);
    return $Property->stat;
}
