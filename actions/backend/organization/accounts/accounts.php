<?php

$Table = helper('Table');

$Query = $Table->getSource();
$Query->andWhere('organization_id = ?', Auth::get()->organization_id);
$Query->andWhere('company_id IS NULL');

$Table->setOption('source', $Query);

Action::set(compact('breadcrumb'));
