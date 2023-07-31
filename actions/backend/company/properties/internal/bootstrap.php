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

$breadcrumb[] = array(
	'label' => 'Internal Information',
	'url'   => url('backend.company.properties'),
	'icon'  => 'sticky-note-o'
);
