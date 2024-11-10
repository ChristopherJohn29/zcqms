<?php

define( 'WP_CACHE', false ); // By SiteGround Optimizer


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
define( 'DB_NAME', 'dbbssrqggvajuz' );

/** Database username */
define( 'DB_USER', 'uvgchxa9ep1l5' );

/** Database password */
define( 'DB_PASSWORD', '1wom1osxcre4' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define('ALLOW_UNFILTERED_UPLOADS', true);

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
define( 'AUTH_KEY',          '+hDLwx2pyps:%.69*wm~ypR>hA_f;XI}XnS?GvNSIJA@RznkeKhGc44gk}]3ju+.' );
define( 'SECURE_AUTH_KEY',   ',?~n!_z$C8N~7Hg]RGdf,@E 290Sw_MN`M  OKPQjPz?ArW3Xoa>li#t?LXA!<WQ' );
define( 'LOGGED_IN_KEY',     '#&ph0bAWLEKY+!5oA)XWaFF)3WlaO&U>d43sEu:;Zc;RG>:CL;})Jz+{jI~6F9m+' );
define( 'NONCE_KEY',         '@N[qau|>Jv~nM*@fdH,QVhxtg<(0J|Iuey;3O>@gu<SSVxf/a_kW oQo/g{~DmBB' );
define( 'AUTH_SALT',         'B@5EU7U2IE= ,>9Uz>|4dRv+`Q<<h`d_flEd/7$M(rg(z2,j,[c`5mXF!Da%wkMr' );
define( 'SECURE_AUTH_SALT',  ':k7hJS4Gtch(_Py=wq zo*_86M? g_)ZX^ZUS0MF@4eYj/sLRXlh7]5Ryi)Q@<Co' );
define( 'LOGGED_IN_SALT',    'xWG!9Mtv}th8?;W!sw&}Ol{Qr#KQ%nc&M: 8>N#MM4Ih!cZ~Cgh?%8]9(R7SZ1nc' );
define( 'NONCE_SALT',        'Iuk]oEc*Fk4UK3w&sS@N=Vcr%fj,0nN4|g>+yC= >Cb_U@+$]-jk_t;[]m)wFm1P' );
define( 'WP_CACHE_KEY_SALT', 'tZHu#(K/Nx<lv.1B)f*N yLkU1A6%`{3X:6?(v*QRRrwqsJ`/`>n;qCy35HYHD$Y' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'dlt_';

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
define('WP_DEBUG', true); // Enable WordPress debugging
define('WP_DEBUG_LOG', true); // Log errors to wp-content/debug.log
define('WP_DEBUG_DISPLAY', false); // Don't display errors on the front-end

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
@include_once('/var/lib/sec/wp-settings-pre.php'); // Added by SiteGround WordPress management system
require_once ABSPATH . 'wp-settings.php';
@include_once('/var/lib/sec/wp-settings.php'); // Added by SiteGround WordPress management system
