<?php

$q = PropertyTable::retrieveBySearch(['luxe' => true]);
$Properties = $q->execute();

$q = new Doctrine_Query();
$q->from('Account a');
$q->leftJoin('a.Branch b');
$q->andWhere('a.show_in_list = 1');
$q->andWhere('a.active = 1');
$q->andWhere('a.luxury = 1');
$q->andWhereIn('a.type', ['Broker', 'Manager']);
$q->orderBy("a.first_name, a.last_name");
$q->groupBy('a.id');

$Brokers = $q->execute();