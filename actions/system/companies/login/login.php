<?php

$Flash = helper('Flash');

$id = get('id');

$q = new Doctrine_Query();
$q->from('Account');
$q->innerJoin('Account.Roles');
$q->innerJoin('Account.Company');
$q->andWhere('Account.Roles.id = ?', 'organization.admin');
$q->andWhere('Account.company_id = ?', $id);
$Account = $q->fetchOne();

//Validate that the model exists
if( empty($Account) ){
    $Flash->error('Could not find company admin to login', url('..'));
}

//Login
Auth::mask($Account->id);

$Flash->success("Succesfully logged as \"{$Account->first_name} {$Account->last_name}\" from \"{$Account->Company->name}\"", url('backend'));
