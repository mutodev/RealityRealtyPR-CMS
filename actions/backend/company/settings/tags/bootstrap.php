<?php

    Auth::requirePermission('account.access');

	$breadcrumb[] = array(
		'label' => 'Tags',
		'url'   => url('backend.company.settings.tags'),
		'icon'  => 'tag'
	);
