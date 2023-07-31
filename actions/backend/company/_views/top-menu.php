<?php

if (Auth::hasPermission('organization:access')) {
    //Companies for drop down menu
    $Query = new Doctrine_Query();
    $Query->from('Company co');
    $Query->andWhere('co.is_active = ?', true);
    $Query->andWhere('co.organization_id = ?', Auth::get()->organization_id);
    $Query->orderBy('co.name');
    $Companies = $Query->fetchArray();
}
