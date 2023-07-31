<?php

if ( isset( $_SERVER['HTTP_X_AJAX_REQUEST'] ) ) {
    require( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ajax.php' );
}

$broker_id = get('broker_id');

$broker = false;

if($broker_id){
	$broker = AccountTable::retrieveById($broker_id);
}

Action::set('broker', $broker);
Action::set( 'page_title', t('translation122') );
