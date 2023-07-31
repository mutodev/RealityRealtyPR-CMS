<?php

$Flash = helper('Flash');

$id = get('id');

$q = new Doctrine_Query();
$q->from('Account');
$q->innerJoin('Account.Roles');
$q->innerJoin('Account.Organization');
$q->andWhere('Account.Roles.id = ?', 'organization.admin');
$q->andWhere('Account.organization_id = ?', $id);
$Account = $q->fetchOne();

//Validate that the model exists
if( empty($Account) ){
    $Flash->error('Could not find organization admin to login', url('..'));
}

//Login
Auth::mask($Account->id);

AccountTable::cleanSession();

$Flash->success("Succesfully logged as \"{$Account->first_name} {$Account->last_name}\" from \"{$Account->Organization->name}\"", url('backend'));
