<?php

	Auth::requirePermission('organization:account.access');

	$breadcrumb[] = array(
		'label' => 'Accounts',
		'url'   => url('backend.organization.accounts'),
		'icon'  => 'user'
	);
