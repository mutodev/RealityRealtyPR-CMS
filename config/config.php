<?php

/* Debuging ======================================================= */
Configure::write("debug", $_ENV['DEBUG'] == 'TRUE' ? true : false);

/* Doctrine ======================================================= */
if ( !Configure::read('debug') && extension_loaded('apc') ) {
    Configure::write('doctrine.cache.query',  true);
    Configure::write('doctrine.cache.result', true);
}

/* APC Cache ====================================================== */
if ( !Configure::read('debug') && extension_loaded('apc') ) {
    Configure::write("system.cache.driver" , 'Apc');
    Configure::write("system.cache.options", array('prefix' => 'rr_'));
}

/* Translation ==================================================== */
Configure::write('translation.languages' , array(
    'en' => 'English',
    'es' => 'Español',
));

Configure::write('lang', 'es');

/* Security ======================================================= */
Configure::write("security.token", $_ENV['SECURITY_TOKEN']);

/* Authentication ================================================= */
Configure::write("auth", false);
Configure::write("auth.salt", '5aWEw#4f#5dS#35&8^5Q1@dDf#4RdSsfnVgf5#5390b90822');
Configure::write("auth.access", false);
Configure::write("auth.auto_persist", false);
Configure::write("auth.cookie", "auth");
Configure::write("auth.remember_me_secs", 60 * 60 * 24 * 7 * 2); //2 Weeks
Configure::write("auth.login_tries_count", 10);
Configure::write("auth.login", 'account.login');
Configure::write("auth.logout", 'account.logout');
Configure::write("auth.onlogin", 'backend.company.campaigns');
Configure::write("auth.onlogout", '');

/* Branding ================================================= */

Configure::write('branding.name', 'Reality Realty');
Configure::write('branding.logo', '/images/logo.png');
Configure::write('branding.favicon', '/favicon.ico');
Configure::write('branding.domain', 'rpm.realityrealtypr.com');
Configure::write('branding.email_logo', 'http://rpm.realityrealtypr.com/images/logo-primary.png');

/* Email ======================================================= */
//Mandrill::setKey('bb2849a6-4dcf-4ddd-8a6e-d31b9498ba93');

/* TimeZone ======================================================= */
//Configure::write('datetime.timezone', 'UTC');

//Enter a address to send copy of all emails sent
Configure::write('mail.copy_to', '');
Configure::write('mail.from.validation', 'no-reply@realityrealtypr.com');

Configure::write('mail.transport'     , 'smtp');
Configure::write('mail.smtp.host', 'email-smtp.us-east-1.amazonaws.com');
Configure::write('mail.smtp.ssl', true);
Configure::write('mail.smtp.port', 587);
Configure::write('mail.smtp.ssl_port', 465);
Configure::write('mail.smtp.username', 'AKIAW7QZ33AW6BT7SZOB');
Configure::write('mail.smtp.password', 'BAU5ML+iYPX1fs18YyhCQp+cYybwVdsxQoZFSCMxqc6p');

Configure::write('mail.sendmail.command', '/usr/sbin/sendmail -bs');


Configure::write('aws.access_key', $_ENV['AMAZON_KEY']);
Configure::write('aws.secret_key', $_ENV['AMAZON_SECRET']);
Configure::write('s3.bucket', "app-propiedades");
Configure::write('s3.base_url', 'https://s3.amazonaws.com/');

require('filesystem.php');
