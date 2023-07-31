<?php

$propertyId = get('id');

$Property = Doctrine::getTable('Property')->find($propertyId);

if($Property){
    $breadcrumb[] = array(
        'label' => '#'.$Property->id .' - '.$Property->title,
        'url'   => url('backend.company.properties.view?id='.$Property->id),
        'icon'  => 'home'
    );
}