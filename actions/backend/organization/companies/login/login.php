<?php

$Flash = helper('Flash');

$id = get('id');

$q = new Doctrine_Query();
$q->from('Company co');
$q->leftJoin('co.Organization');
$q->andWhere('co.id = ?', $id);
$q->andWhere('co.organization_id = ?', Auth::get()->organization_id);
$Company = $q->fetchOne();

//Validate that the model exists
if( empty($Company) ){
    $Flash->error('Could not find company to login', url('..'));
}

Auth::get()->setActiveCompanyId($Company->id);

redirect('backend.company');
