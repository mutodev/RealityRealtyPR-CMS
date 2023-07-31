<?php

    Auth::requirePermission('account.access');

	$breadcrumb[] = array(
		'label' => 'Accounts',
		'url'   => url('backend.company.accounts'),
		'icon'  => 'user'
	);
