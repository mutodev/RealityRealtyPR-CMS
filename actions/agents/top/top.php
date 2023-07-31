<?php

$Pagination = helper('Pagination');

$username = get('username');

$Query = new Doctrine_Query();
$Query->from('Account a');
$Query->andWhere('a.username = ?', $username);
$Query->andWhere('a.is_top_agent = 1');
$Agent = $Query->fetchOne();

if (!$Agent) {
    die('No top agent');
}

$q = PropertyTable::retrieveBySearch($_GET, $Agent);

$Pagination->setSize(15);
$q = $Pagination->paginate($q);
$Properties = $q->execute();

Action::setLayout('agent');
Action::set(compact('Properties', 'Agent'));
