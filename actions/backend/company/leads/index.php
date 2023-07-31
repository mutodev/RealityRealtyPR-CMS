<?php

$Table = helper('Table');

$q = new Doctrine_Query();
$q->from('Lead');
$q->leftJoin('Lead.Property');
$q->leftJoin('Lead.Source');
$q->leftJoin('Lead.Searches s');
$q->leftJoin('s.Area');
$q->leftJoin('s.Category');
$q->orderBy('Lead.created_at DESC');

if (!Auth::hasPermission('property.manage')) {
	$q->andWhere('Lead.Property.account_id = ? OR Lead.account_id = ?', array(Auth::get()->id, Auth::get()->id));
}

$Table->setOption('source', $q);

Action::set(compact('breadcrumb'));

