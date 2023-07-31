<?php

$id = get('id');

if( $id ){

	$Property = Doctrine::getTable('Property')->find( $id );

	$title = '#'.$Property->id .' - '.$Property['title'];

	if ($Property->contract_id) {
        $title .= " <span style=\"padding: 10px; border-radius: 8px; font-size: 20px;\" class=\"badge " . (in_array($Property->Contract->status, array(
                0 => 'Optioned',
                1 => 'Closed',
                3 => 'Rented',
                5 => 'Out of Market',
            )) ? 'badge-danger' : 'badge-info') . "\">" . $Property->Contract->status . "</span>";
    }

	$breadcrumb[] = array(
		'label' => $title,
	);
}
