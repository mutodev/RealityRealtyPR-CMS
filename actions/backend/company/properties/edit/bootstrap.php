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
    return AccountTable::accountsByRoleForSelect(true);
}

function getCompanies($element){
    return EntityTable::entitiesForSelect($element,'COMPANY');
}

function getBanks($element){
    return EntityTable::entitiesForSelect($element, 'BANK');
}

function getInvestor($element){
    return EntityTable::entitiesForSelect($element, 'INVESTOR');
}

function get_categories($element) {
    $q = new Doctrine_Query();
    $q->from('PropertyCategory c');
    $q->andWhere('c.type = ?', $element->getForm()->get('category_type')->getValue());
    $q->orderBy('name ASC');

    $categories = array();

    foreach($q->execute() as $Category) {
        $categories[$Category->id] = $Category->name;
    }

    return $categories;
}

function dependResidential($element) {
    $type = $element->getForm()->get('category_type')->getValue();

    return $type == 'Residential' || $element->getForm()->get('category_id')->getValue() == 23;
}
