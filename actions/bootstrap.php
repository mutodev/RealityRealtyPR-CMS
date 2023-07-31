<?php

//error_reporting(0);
//ini_set('display_errors', 0);

if($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'http') {
    header("Status: 301 Moved Permanently");
    header(sprintf(
        'Location: https://%s%s',
        $_SERVER['HTTP_HOST'],
        $_SERVER['REQUEST_URI']
    ));
    exit();
} else {
    $_SERVER['HTTPS'] = 'on';
}

$lang = Session::read('lang', 'es');

Translate::setLanguage( $lang );
Configure::write('lang', $lang);
