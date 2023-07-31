<?php

if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = 443;
}

/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'website');

/** MySQL database username */
define('DB_USER', 'realityrealty');

/** MySQL database password */
define('DB_PASSWORD', 'esxvuQ2C4SJhTmvCqw');

/** MySQL hostname */
define('DB_HOST', 'realityrealty-prod.cielwkgr0tvv.us-east-1.rds.amazonaws.com');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'S ~6W[] SIfH>C}J+BB#M0-M|eTo](Q80KRd+J|!}_0l.P?0=1m*8`4AEb~|2^Bc');
define('SECURE_AUTH_KEY',  'T#/bll2CWv$^[Zrm]9RZ|0}AU;_*`V|4/o)2S](ck&vYJ@,jIA1A|hd{nfrZ+EA~');
define('LOGGED_IN_KEY',    'L7$Q.Nmw:/Ru0+&DgZ87D.JCNx9mjCk:7xN8)7yp*8G[>Oj~UG>,9tg+ztVy/nK_');
define('NONCE_KEY',        'H%ekGSN|j`|x=c|0#PJej#-e.9*It!o-:7RMaP`1+37^qng/syq-G|</^L- ?b>m');
define('AUTH_SALT',        'Nu)8)0`^|HpyXfYjy@,[{ IarpoF)!}Z;1>Xf| LGDch1%N%nU[!t^{dJFvY<pOL');
define('SECURE_AUTH_SALT', 'Ri^zL.]?l[ks_)}fiDgzmym3O]%EXe[A+btH-:1od</}5(#LLJ z1^|0~&c-kf0?');
define('LOGGED_IN_SALT',   'B?8lT!BU@+ZjD2ZjMXyAe|R)|~V}~yvITjDCd|>|!JKW?m= z_Wq;:s}bi|g`iB?');
define('NONCE_SALT',       'P~!eS+>eSWg(j#=7>pxFy80YZA1]x* h|]-LA@d~6ow)e|0Uzo=Qz02R!A/2W@?k');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress.  A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de.mo to wp-content/languages and set WPLANG to 'de' to enable German
 * language support.
 */
define ('WPLANG', 'es_ES');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
