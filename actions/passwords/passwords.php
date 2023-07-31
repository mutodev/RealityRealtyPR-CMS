<?php

$q = new Doctrine_Query();
$q->from('Account');
$q->andWhere('email LIKE ?', '%realityrealtypr.com%');
$Accounts = $q->execute();

foreach($Accounts as $Account){
	$username = str_replace('@realityrealtypr.com', '', $Account->email);

	$Account->setPassword($username.'123');
	$Account->username = $username;
	$Account->save();
}

prd('Termino');