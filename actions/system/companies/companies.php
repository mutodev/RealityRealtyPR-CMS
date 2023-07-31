<?php

$Table  = helper('Table');

//Mediums
$Query = new Doctrine_Query();
$Query->from('Medium');

$mediums = array();
foreach ($Query->fetchArray() as $row) {
    $mediums[$row['slug']] = $row['id'];
}

$Query = new Doctrine_Query();
$Query->addSelect('co.*');
$Query->addSelect('COUNT(DISTINCT ac.id) as total_accounts');
$Query->addSelect('COUNT(DISTINCT ca.id) as total_campaigns');
$Query->addSelect('COUNT(DISTINCT so.id) as total_sources');
$Query->addSelect('COUNT(DISTINCT r_numbers.resource_id) as total_resources_numbers');
$Query->addSelect('COUNT(DISTINCT r_emails.resource_id) as total_resources_emails');
$Query->from('Company co');
$Query->leftJoin('co.Accounts ac');
$Query->leftJoin('co.Campaigns ca');
$Query->leftJoin('ca.Sources so');
$Query->leftJoin('co.Resources re');
$Query->leftJoin('re.MediumResource r_numbers WITH (r_numbers.medium_id = '.$mediums['CALL'].' OR r_numbers.medium_id = '.$mediums['SMS'].')');
$Query->leftJoin('re.MediumResource r_emails WITH (r_emails.medium_id = '.$mediums['EMAIL'].')');
$Query->leftJoin('co.Subscription.Plan p');
$Query->groupBy('co.id');

$Table->setOption('source', $Query);

Action::set(compact('breadcrumb'));
