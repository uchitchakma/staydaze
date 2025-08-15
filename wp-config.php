<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          '[ix|/]U[C5cEGhjYy=1|x-L$%<_py}v/<>1-h^5l1{9X2z15JrXohEfjoIg#do<P' );
define( 'SECURE_AUTH_KEY',   'eLM=}e[1:_B,VTgmL^9twb60`O_Jxa8]lcW(2FBAG5UtcpI#Zghu*YvSw[}8%rrm' );
define( 'LOGGED_IN_KEY',     'v{c`>+A6x_Cpn9}H<SbWoZ#D0oU6kP7L,S2HJu$i?|G:Z9f@sLp_h#3i-zV]E{IC' );
define( 'NONCE_KEY',         '&zX 6@. iVLxaSH.kVz)&Qa)Uf8y!F:m^yrRi{ZE~je8xmnM9O/?aRVr>}DJ8j5s' );
define( 'AUTH_SALT',         '9l6=n.~v<d<xxe#rF{t=c^f@G<J6-2)~ws*[CS?/DR Q8htr7tq3Cb.+:3KA!N~c' );
define( 'SECURE_AUTH_SALT',  '5-45TjM7}@Sd{qP4dJ,BYWQ;Mo4.FFV[T1e^y^eZF%XWooD5DeDG>bA$?%WCcg-,' );
define( 'LOGGED_IN_SALT',    'hCBzSBr=}Cn|&{twAMQHOwNW)?/EBXy% HzCWzZptY<&:U=]z[I+VjAcdV|TA>gO' );
define( 'NONCE_SALT',        'Gt7|  eMb+H[h&x&528P*];-Y#<:z8=de{-I(fFly|m3>%5y<CuohJ_Xz~:i,B]/' );
define( 'WP_CACHE_KEY_SALT', 'oKI(84cc&%6ifhp:K7lA0[%-ryPcf*g={urg%;2tN)h4Ap/^I^h[b?6d91e=dLAE' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
