<?php

$Table = helper('Table');
$Search = helper('Search');
$Pagination = helper('Pagination');

$filters = $Search->getSearch();

$action = get('action');
$status = get('status', 'active');

$q = new Doctrine_Query();
$q->select('COUNT(DISTINCT PropertyStat.id) as stat, PropertyStat.*, Property.*');
$q->from('PropertyStat');
$q->innerJoin('PropertyStat.Property Property');
$q->innerJoin('Property.Photos');
$q->innerJoin('Property.Category');
$q->innerJoin('Property.Area');
$q->groupBy('PropertyStat.property_id');
$q->orderBy('Property.end_at ASC');

if ($status === 'active') {
    //$q->andWhere('Property.status = ?', 'PUBLISHED');
    $q->andWhere('Property.end_at >= ?', date('Y-m-d 00:00:00'));
} else if ($status === 'inactive')  {
    $q->andWhere('Property.end_at < ? OR Property.status = \'UNPUBLISHED\'', date('Y-m-d 00:00:00'));
}

if (!Auth::hasPermission('property.manage')) {
	$q->andWhere('Property.account_id = ? OR Property.secondary_account_id = ?', array(Auth::get()->id, Auth::get()->id));
}

//die($q->getSqlQuery());
//
//prd($q->fetchArray());

if ($Search->getDQL()) {
    $q->andWhere($Search->getDQL());
}

$results = $q->execute();

if ($action == 'export') {
    Action::setLayout('plain');

    $Table->setOption('pagination', false);
    $Table->removeColumn('actions');
}

Action::set(compact('breadcrumb', 'action', 'results'));

