<?php

Auth::requirePermission('organization:access');

Yammon::addViewsPath(__DIR__ . DS . '_views');

$Menu = helper('Menu');

$Menu->add( "companies"  , array(
    "label"      => '<i class="fa fa-building"></i>'.t('Companies') ,
    "link"       => "backend.organization.companies" ,
    "permission" => "organization:company.access" ,
));
$Menu->add( "accounts"  , array(
    "label"      => '<i class="fa fa-user"></i>'.t('Accounts') ,
    "link"       => "backend.organization.accounts" ,
    'permission' => 'organization:account.access'
));
$Menu->add( "logout"  , array(
    "label"      => '<i class="glyphicon glyphicon-log-out"></i>'.t('Logout') ,
    "link"       => "account.logout" ,
));
