<?php
/** Enable W3 Total Cache Edge Mode */
define('W3TC_EDGE_MODE', true); // Added by W3 Total Cache


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
define('DB_NAME', 'liwts_live');

/** MySQL database username */
define('DB_USER', 'liwts_masteruser');

/** MySQL database password */
define('DB_PASSWORD', 'Ce*y-Ddb66B~');

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
define('AUTH_KEY',         '95kTe-G1/|[{A+0g?ydAHa+u~&U=43;)#TbT]yg]|PO1_upN5.Ui/~IhhdN*di>B');
define('SECURE_AUTH_KEY',  'B,u_kDK|5Oqxpq|S-i5KNg!MQD:l?qSCA!,~+(iRzg`U.NZq,MYW09E.(JV8?(3}');
define('LOGGED_IN_KEY',    'sxIOvDYOE`g9iFeb,=GVX?+im]F+8|EC&,^:,D-P=q7W[DLUQ#6LHV^G`z;-0,Ow');
define('NONCE_KEY',        '65AT2g/?h!QD2<4+Icpic/U|lRykT&cu-#umgkCM2kON+hqxrW1:-q[Fh}-or;=3');
define('AUTH_SALT',        'AX9m}+YF4}n`HrJ0DLB0?M$~M_(nIa+T9@~!@BPRO!=6A(V5;Tx.(4{8X.0D1bGU');
define('SECURE_AUTH_SALT', '&SRWD?nJV|26mp-G@kYO-L++{ 9y~gzYvG9KJP@|k7<7y`grz||-S.N50d.mIHkM');
define('LOGGED_IN_SALT',   'Gb<I+shaBj8Y>J?`b-,TCUV@H#pMBd0~O.JA;HT?8h$.Jjl#xTtN<^9=wW<`X*nq');
define('NONCE_SALT',       '^E>_{niIUTr!J?{$Z5wT AsN?T|&)C|5uN. E81pZN1,DC^<2@,f.)t[4W*5;{OC');

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
