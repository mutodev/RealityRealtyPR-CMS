<?php

Auth::requirePermission('account.manage');

$breadcrumb[] = array(
	'label' => get('id') ? t('Edit Property Category') : t('New Property Category'),
	'icon'  => 'home'
);