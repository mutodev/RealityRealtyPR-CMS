<?php

$csrfToken = md5(Configure::read("security.token") . session_id());

$Query = new Doctrine_Query;
$Query->select('*');

if ($Subscription->plan_id && $Subscription->custom_price) {
    $Query->addSelect('IF(id = '.$Subscription->plan_id.', '.$Subscription->custom_price.', price) as price');
}

$whereCondition = array('is_active = ?', 'is_public = ?');
$whereValues    = array(1, 1);

if ($country) {
    $whereCondition[] = 'iso_country_code = ?';
    $whereValues[]    = $country;
}

$Query->from('BillingPlan');
$Query->where(implode(' AND ', $whereCondition), $whereValues);
$Query->orWhere('id = ?', $Subscription->plan_id ? $Subscription->plan_id : 0);
$Query->orderBy('price ASC');
$Plans = $Query->execute();
