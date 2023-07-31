<?php

$Query = new Doctrine_Query();
$Query->from('Goal g');
$Query->andWhere('g.account_id = ?', Auth::getId());
$Query->andWhere('g.year = ?', date('Y'));
$Goal = $Query->fetchOne();
$goalStats = $Goal ? $Goal->getStats(date('m')) : [];

Action::set(compact('goalStats'));
