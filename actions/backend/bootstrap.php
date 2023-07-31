<?php

Translate::setLanguage( 'en' );

Auth::requireLogin();

Auth::requirePermission('backend.access');

//Set company or organization timezone
Configure::write('datetime.timezone', Auth::get()->getActiveCompany()->timezone);

$breadcrumb = array();
$breadcrumb[] = array(
	'label' => 'Dashboard',
	'icon'  => 'home',
    'url'   => url('backend'),
);

Action::setLayout('backend');
