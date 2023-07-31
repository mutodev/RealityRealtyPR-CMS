<?php

$Query = new Doctrine_Query();
$Query->from('Account a');
$Query->andWhere('a.is_top_agent = 1');
//$Query->limit(4);
$Query->andWhere('a.active = 1');
$Query->orderBy('a.first_name, a.last_name');
$TopAgents = $Query->execute();
