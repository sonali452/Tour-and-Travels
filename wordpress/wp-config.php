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
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'TandT' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
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
define( 'AUTH_KEY',         '2zuc=*-p2PO#TFd(pxgsE9hLTQ[s?5fV8IU/eXwG..|S;~R>]{Zs3?PCTTj*` tv' );
define( 'SECURE_AUTH_KEY',  'xhjP#ElVv_LDaX-0S[4lIbHcBgaf}1AGUIPB)6/6x0juHh*f3iA/W:|DGa@(zS5x' );
define( 'LOGGED_IN_KEY',    '?-(g2vTOd4!)rIfQ2wO${PM&|W~XWp{}YPi`{fCYT.vJ4LpcJKd]ey,Th|K>BO[V' );
define( 'NONCE_KEY',        '53r:lZ+r({D](>8h]}7m@<4%w2+St:g.i3jU*KiD,;3,Q+x^D?wv%FUA$QSRfjj:' );
define( 'AUTH_SALT',        '`tZPh<sDA8_UGuWRiJP4P7_qyU(7U/RpNfdj<rH^r<:4dd}w3.<?`HQK{xhKWW|U' );
define( 'SECURE_AUTH_SALT', 'y.m-y[wJ8Xi} L=C71Z1`znw}&%UTq0$jri1_)YR8P^RMx7v5{/B2wnK1Ln9hV+a' );
define( 'LOGGED_IN_SALT',   'X?Jd39C=R5wTNzQ;V0wj1RUJ1^&<`N6a^%Dj>Bc`&MF?7hG_a|id6vtZ[#vMYr/1' );
define( 'NONCE_SALT',       'I!PbALn:^m#cEB)[;[ay{dPdt{!gugV(@Wu}VwfEZO5((f5:?dIrzvtO=B->{rRw' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
