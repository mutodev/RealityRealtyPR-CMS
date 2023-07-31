<?php

    Auth::requirePermission('organization:company.access');

	$breadcrumb[] = array(
		'label' => 'Companies',
		'url'   => url('backend.organization.companies'),
		'icon'  => 'user'
	);
