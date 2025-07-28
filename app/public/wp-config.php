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
define( 'AUTH_KEY',          'mQt-k-{nI>,iBCIm:|O%8 /u@H&hE`p_4D{r3zagxI;=EPt3fQrGUi^E@vK%tPfJ' );
define( 'SECURE_AUTH_KEY',   'y|-q]q=Re8]jZ/[Y1}Z=p=1)EW@zf{I5X07]@},%v[vJ|].}ou<Xt-di1$2YUsll' );
define( 'LOGGED_IN_KEY',     'zgiS=<)/CYwl+HSiARdGB[83VDLxUF>W,>uS;[4#wh[mb+`HMC><fbmu]y[5bT[n' );
define( 'NONCE_KEY',         'KH d{d=5NPa=AL%f<lqL(ZtOzNi>#nD!qjQMRiJ3do!.;e+qUdk~`#2Z,4nRcVwi' );
define( 'AUTH_SALT',         'ce_a1f#fZIr#?1d9+.tUR4D<:P?$`ya.mD)I|Ou6K=uJ}1c~edQo3U2c*R*s> }<' );
define( 'SECURE_AUTH_SALT',  'I5<fKCrB`GLzz--r}gU[X +}fbAPh+P#nsl0<]8:kP<=8(wZZ^B?|KV?^b5M!bnx' );
define( 'LOGGED_IN_SALT',    '[XqT+mPq1yLxyE*YdwTRn:Z-Nc~$SZ8c,DUbSX{2&*?~ZQw|yS>y.d4#>I,_faL$' );
define( 'NONCE_SALT',        'S>K51a;4{hRlu|};]rodw^n5,RANqvzpUsG^F7N<iUY(XljMekF_M|Edfq64983P' );
define( 'WP_CACHE_KEY_SALT', '|~>5Ir1+d`DaPa?@]}x6Krg6[3nWr1h3*o]#b t s$KA{QoixO6~+a^f(?5~9?s?' );


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
