<?php

$Table  = helper('Table');
$Search = helper('Search');

$id = get('property_id');

$Query = new Doctrine_Query();
$Query->from('PropertyOffer o');
$Query->leftJoin('o.Property p');
$Query->andWhere('p.company_id = ?', Auth::get()->getActiveCompany()->id);
$Query->andWhere('o.property_id = ?', $id);
$Query->orderBy('created_at DESC');

$Table->setOption('source', $Query);

Action::set(compact('breadcrumb','id'));

