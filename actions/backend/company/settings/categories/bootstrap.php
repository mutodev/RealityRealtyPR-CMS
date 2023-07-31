<?php

    Auth::requirePermission('account.access');

	$breadcrumb[] = array(
		'label' => 'Property Categories',
		'url'   => url('backend.company.settings.categories'),
		'icon'  => 'home'
	);
