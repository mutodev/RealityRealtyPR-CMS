<?php

Auth::requirePermission('account.manage');

$breadcrumb[] = array(
	'label' => get('id') ? t('Edit Tag') : t('New Tag'),
	'icon'  => 'tag'
);
