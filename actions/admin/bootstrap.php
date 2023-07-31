<?php

if( !Auth::get('username') ) {
  header('Location: /accounts/login');
}

Action::setLayout( 'admin' );

$sidemenu = helper('Menu' );

$sidemenu->add( "dashboard"  , array(
    "label"       => t('Dashboard') ,
    "link"        => "admin" ,
));

$sidemenu->add( "blog"  , array(
    "label"       => t('Blog') ,
    "link"        => "/blog/wp-admin/" ,
));

$sidemenu->add( "accounts"  , array(
    "label"       => t('Accounts') ,
    "link"        => "admin.accounts" ,
    "icon"        => "/img/icons/accounts.gif" ,
    "active"      => "admin.accounts" ,
    "description" => t("Manage user accounts")
));

$sidemenu->add( "logout"  , array(
    "label"       => t('Logout') ,
    "link"        => "accounts.logout" ,
));