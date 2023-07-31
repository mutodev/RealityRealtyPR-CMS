<?php

/* MySQL ======================================================== */

$parts = parse_url($_ENV['DATABASE_URL']);

Configure::write("database.host"     , $parts['host'] );
Configure::write("database.port"     , $parts['port'] );
Configure::write("database.username" , $parts['user'] );
Configure::write("database.password" , $parts['pass'] );
Configure::write("database.database" , substr($parts['path'], 1) );

if ($_ENV['DATABASE_SSL'] == 'TRUE') {
    Configure::write("database.options", array(
        PDO::MYSQL_ATTR_SSL_CA => dirname(__FILE__) . "/mysql-ssl-ca-cert.pem"
    ));
}

/* REDIS ======================================================== */

if (isset($_ENV['REDIS_URL'])) {
    $parts = parse_url($_ENV['REDIS_URL']);

    Configure::write('session.handler', 'redis');
    Configure::write('session.redis.host', $parts['host']);
    Configure::write('session.redis.port', $parts['port']);
    Configure::write('session.redis.auth', $parts['pass']);
}

