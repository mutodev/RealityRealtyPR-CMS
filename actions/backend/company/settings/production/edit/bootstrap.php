<?php

Auth::requirePermission('account.manage');

$breadcrumb[] = array(
	'label' => get('id') ? t('Edit Entity') : t('New Entity'),
	'icon'  => 'building'
);