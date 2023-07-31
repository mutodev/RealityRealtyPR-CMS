<?php

Auth::requirePermission('document.access');

$breadcrumb[] = array(
	'label' => 'Documents',
	'url'   => url('backend.company.documents'),
	'icon'  => 'file-text-o'
);

function renderCategory($Document){

    $html = array();
    if($Document->Category->parent_id){
        $html[] = $Document->Category->Parent->name;
    }

    $html[] = $Document->Category->name;

    return implode(' - ', $html);
}