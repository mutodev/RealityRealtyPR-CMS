<?php

Translate::setLanguage('en');

Auth::requireLogin();

Auth::requirePermission('system.admin');

date_default_timezone_set('America/Puerto_Rico');

$breadcrumb = array();
$breadcrumb[] = array(
	'label' => 'System Management',
	'icon'  => 'magic',
    'url'   => url('system'),
);

$Menu = helper('Menu');

$Menu->add("organizations"  , array(
    "label"      => '<i class="fa fa-building-o"></i>'.t('Organizations') ,
    "link"       => "system.organizations"
));
$Menu->add("accounts"  , array(
    "label"      => '<i class="fa fa-users"></i>'.t('Accounts') ,
    "link"       => "system.accounts"
));
$Menu->add( "logout"  , array(
    "label"      => '<i class="glyphicon glyphicon-log-out"></i>'.t('Logout') ,
    "link"       => "account.logout" ,
));

Action::setLayout('backend');

Action::set(compact('breadcrumb'));
