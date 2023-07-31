<?php

$Table = helper('Table');

$Query = $Table->getSource();
$Query->andWhere('company_id = ?', Auth::get()->getActiveCompany()->id);
$Table->setOption('source', $Query);

Action::set(compact('breadcrumb'));
