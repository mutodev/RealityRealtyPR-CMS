<?php

Auth::requirePermission('document.manage');

$breadcrumb[] = array(
	'label' => 'Manage Categories',
	'url'   => url('backend.company.documents'),
	'icon'  => 'file-text-o'
);

function renderCats($Category){

    $html = array();
    if($Category->parent_id){
        $html[] = $Category->Parent->name;
    }

    $html[] = $Category->name;

    return implode(' - ', $html);
}