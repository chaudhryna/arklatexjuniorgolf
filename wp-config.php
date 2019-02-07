<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'arklatex');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         '5yD{RgZ9~O!::^_agG;9gRDzWX3<l!c5Wz#v{?bZ`rmRX#pQ~,~~k_<~o(WnL(%N');
define('SECURE_AUTH_KEY',  'zi>6v]yXNS?QwXGYNE2aO2wHEdzSdthSDe5?jB]{-;hilTIH0#=#D~%8EY^D{gsG');
define('LOGGED_IN_KEY',    '}Yq[&fs*+ggf#ezPy:u)[]Ib]ur~3v`d/u,|Hv.K40{h*Rlhc2/0J!kq[){)RQ!V');
define('NONCE_KEY',        'pLw]Tul%w0_Fg^;cuX/!=F>W24bF%Ic}>*]]qo/Z$Y7wbT,*`}_GB1-qn%ZR9_Aq');
define('AUTH_SALT',        'v+c8e]Z&+JoDE?:t+l6>m(eW~_q, M/e/|?Nl-fA>&Wg+>Ku?gT9H#^!NmZ2$r;-');
define('SECURE_AUTH_SALT', 'dO!pk1`:mb|tII^KrtPi7s5t5-4?M99i6hpzYQW~O,BxXzsH|Xn]kML01]U5OlP}');
define('LOGGED_IN_SALT',   'G6gV3x*)EMb%>7A4hMnuUqS8w&2d=&-% A4Tp_3(~<>CL#dsDqg2P2BeV(Z^DmPJ');
define('NONCE_SALT',       '2nBqafuT_iSzLuPris:9bNTic$Xg~.}E*@*&S~9; .f4lQ**Tijd`Lok3u&<18-7');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
