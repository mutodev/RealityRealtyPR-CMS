<?php

Auth::requirePermission('organization:account.manage');

$breadcrumb[] = array(
	'label' => get('id') ? t('Edit Account') : t('New Account'),
	'icon'  => 'user'
);
