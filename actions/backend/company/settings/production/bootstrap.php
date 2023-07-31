<?php

    Auth::requirePermission('account.access');

	$breadcrumb[] = array(
		'label' => 'Production Units',
		'url'   => url('backend.company.settings.production'),
		'icon'  => 'line-chart'
	);
