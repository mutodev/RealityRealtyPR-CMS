<?php

$Table = helper('Table');
$Search = helper('Search');

$filters = $Search->getSearch();

$action = get('action');
$status = get('status', 'active');

$q = new Doctrine_Query();
$q->from('Property');
$q->leftJoin('Property.Photos');
$q->leftJoin('Property.Category');
$q->leftJoin('Property.Contract c');
$q->leftJoin('Property.Area');

if ($status === 'active') {
    //$q->andWhere('Property.status = ?', 'PUBLISHED');
    $q->andWhereNotIn('c.status', ['Rented', 'Closed', 'Out of Market']);
    $q->andWhere('Property.end_at >= ?', date('Y-m-d 00:00:00'));
} else if ($status === 'inactive')  {
    $q->andWhere("c.status IN ('Closed', 'Rented', 'Out of Market') OR (Property.end_at < ? OR Property.end_at IS NULL)", date('Y-m-d 00:00:00'));
}

//if (!Auth::hasPermission('property.manage')) {
//	$q->andWhere('Property.account_id = ? OR Property.secondary_account_id = ?', array(Auth::get()->id, Auth::get()->id));
//}

$Table->setOption('source', $q);

if ($action == 'export') {
    Action::setLayout('plain');

    $Table->setOption('pagination', false);
    $Table->removeColumn('actions');
}

$PadDocument = Doctrine::getTable('Document')->find(80);

Action::set(compact('breadcrumb', 'action', 'PadDocument'));

