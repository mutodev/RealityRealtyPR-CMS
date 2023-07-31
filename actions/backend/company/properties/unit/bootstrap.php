<?php

$propertyId = get('id');

$Property = Doctrine::getTable('Property')->find($propertyId);

if($Property){

    $breadcrumb[] = array(
        'label' => '#'.$Property->id .' - '.$Property->title,
        'url'   => url('backend.company.properties.view?id='.$Property->id),
        'icon'  => 'home'
    );
}else{
    $breadcrumb[] = array(
        'label' => t('New Property'),
        'url'   => url('backend.company.properties'),
        'icon'  => 'home'
    );
}

function filterAgents($element){

    $result   = array();


    $Query = new Doctrine_Query;
    $Query->select('a.id, a.first_name, a.last_name');
    $Query->from('Account a');
    $Query->andWhere('a.active = 1');
    $Query->andWhere('a.company_id = ?', Auth::get()->getActiveCompany()->id);

    $Query->orderBy('a.first_name ASC, a.last_name ASC');

    foreach ($Query->fetchArray() as $account) {
        $result[ $account['id'] ] = $account['first_name'] . ' ' . $account['last_name'];
    }

    return $result;
}
