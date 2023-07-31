<?php

    Auth::requirePermission('account.access');

	$breadcrumb[] = array(
		'label' => 'Entities',
		'url'   => url('backend.company.settings.entities'),
		'icon'  => 'building'
	);
