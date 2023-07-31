<?php

$Query = new Doctrine_Query();
$q = new Doctrine_Query();
$q->from('Property');
$q->innerJoin('Property.Photos');
$q->leftJoin('Property.Category');
$q->leftJoin('Property.Contract c');
$q->leftJoin('Property.Area');
$q->andWhere('Property.contract_id IS NOT NULL');
$q->andWhereNotIn('c.status', ['Rented', 'Closed', 'Out of Market']);
$q->orderBy('Property.created_at DESC');
$q->limit(10);
//$q->andWhere('Property.created_at >= ?', date('Y-m-d 00:00:00', strtotime('-7 days')));
$Properties = $q->execute();
