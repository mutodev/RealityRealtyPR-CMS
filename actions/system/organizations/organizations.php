<?php

$Table  = helper('Table');

$Query = new Doctrine_Query();
$Query->addSelect('og.*');
//$Query->addSelect('COUNT(DISTINCT ac.id) as total_accounts');
//$Query->addSelect('COUNT(DISTINCT ca.id) as total_campaigns');
//$Query->addSelect('COUNT(DISTINCT so.id) as total_sources');
//$Query->addSelect('COUNT(DISTINCT r_numbers.resource_id) as total_resources_numbers');
//$Query->addSelect('COUNT(DISTINCT r_emails.resource_id) as total_resources_emails');
$Query->from('Organization og');
//$Query->leftJoin('og.Companies co');
//$Query->leftJoin('og.Accounts ac');
//$Query->leftJoin('co.Campaigns ca');
//$Query->leftJoin('ca.Sources so');
//$Query->leftJoin('co.Resources re');
//$Query->leftJoin('re.MediumResource r_numbers WITH (r_numbers.medium_id = '.$mediums['CALL'].' OR r_numbers.medium_id = '.$mediums['SMS'].')');
//$Query->leftJoin('re.MediumResource r_emails WITH (r_emails.medium_id = '.$mediums['EMAIL'].')');
//$Query->leftJoin('og.Subscription.Plan p');
$Query->groupBy('og.id');

$Table->setOption('source', $Query);

Action::set(compact('breadcrumb'));
