<?php
$Form  = helper('Form');
$Flash = helper('Flash');

$Form->setOption('action', 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);

$status = '';

//Get variables
$target      = urldecode(get('target'));
$username    = post('username');
$password    = post('password');
$remember_me = post('remember_me');

if (Auth::isLoggedIn() && $target) {
    redirect($target);
}

if (isset($_POST['username'])) {

    AccountTable::cleanSession();

    $status = Auth::login( $username , $password , $remember_me , $target ? $target : 'index' );
}

if ($status) {
    $Form->setError( (string)$status );
}

Action::set(compact('username','remember_me'));
