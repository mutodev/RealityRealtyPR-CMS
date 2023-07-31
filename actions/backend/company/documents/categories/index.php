<?php

$Table  = helper('Table');
$Search = helper('Search');

$Query = new Doctrine_Query();
$Query->from('DocumentCategory c');
$Query->andWhere('c.company_id = ?', Auth::get()->getActiveCompany()->id);

$Table->setOption('source', $Query);

Action::set(compact('breadcrumb'));

