<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp_healthray_old' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '!>$4DxOGC8/%,!yWm;hE3Lbjvwd6#/G*(;B^=Z( QcG0CvO>%, fO#=,Vg;ySOxa' );
define( 'SECURE_AUTH_KEY',  'e}aCJg+yxOcArao$[y<yku:^2_Z[5s][R0{2{k.GI]G^[Ov50B|<ScNNEuZ)f}Vy' );
define( 'LOGGED_IN_KEY',    'R{;UW5QGP@,y*<;f609-puCC86=M UW~m&``;p(H*v:53V5nCGE+Dm6wYG8hgfB3' );
define( 'NONCE_KEY',        'zRQ>J%puCyZlcGiMTS{u%O Ru<)7f]eL_=5k*sUXj_X!1kYfYW1+NwhNml{J((=g' );
define( 'AUTH_SALT',        'z1E*lb&bwykvx} j/OM+gS{/AXL~e^PO^o=m`VAc|P*vCzBt1o,.=;$08%!*)ntB' );
define( 'SECURE_AUTH_SALT', 'Sg@M4UI3IO`q6h9$J9M@|j4H,0u>FYIjMTOQnN/}>p=UOG>F@}cCB <2pnereaV,' );
define( 'LOGGED_IN_SALT',   '^-8>ql,Ve<MwsbC;=_rtKy}Bm3ZN()Aw4f%5oiZ&|R/NYUh}PZlR)>MTUF*//<up' );
define( 'NONCE_SALT',       '0e]/4?Rnx1G`;7KZs|4([,59O][CF|9fMUQ/#uE@uf$nEl!Wk]yIFVq-<DA@h6<^' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
