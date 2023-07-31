<?php

$Query = new Doctrine_Query();
$Query->from('Goal g');
$Query->andWhere('g.account_id = ?', Auth::getId());
$Query->andWhere('g.year = ?', date('Y'));
$Goal = $Query->fetchOne();
$goalStats = $Goal ? $Goal->getStats('04') : [];

$Query = new Doctrine_Query();
$Query->from('Lead l');
$Query->leftJoin('l.Source s');
$Query->andWhere('l.account_id = ?', Auth::get()->id);
$Query->andWhere('l.created_at >= ?', date('Y-m-d 00:00:00', strtotime('-7 days')));
$Query->orderBy('l.created_at DESC');
$Leads = $Query->execute();

Action::set(compact('goalStats', 'Leads', 'Properties'));
