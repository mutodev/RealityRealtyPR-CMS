<?php

$Table  = helper('Table');
$Search = helper('Search');

$id = get('property_id');

$Query = new Doctrine_Query();
$Query->from('Contract c');
$Query->leftJoin('c.Property p');
$Query->leftJoin('c.Client cl');
$Query->andWhere('p.company_id = ?', Auth::get()->getActiveCompany()->id);
$Query->andWhere('c.property_id = ?', $id);
$Query->orderBy('created_at DESC');

$Table->setOption('source', $Query);

Action::set(compact('breadcrumb','id'));

