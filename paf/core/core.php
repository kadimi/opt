<?php

/**
* Main core file of the "Plugin Options" framework
* 
* The file loads the different parts of the framework
* 
* @package plugin-admin-framework
*/

/**
* Helps tracking that the framework core was loaded
*
* @var bool
*/
define( 'PLUGIN_OPTIONS', 1 );

/**
 * Load the class K
 */
if ( ! class_exists( 'K' ) ) {
	require dirname( __FILE__ ) . '/lib/K/K.php';
}

/**
 * Load the class Kint
 */
if ( ! class_exists ( 'Kint' ) ) {
    require dirname( __FILE__ ) . '/lib/kint/Kint.class.php';
}

/**
 * Include options files
 */
foreach ( array( 'pages', 'tabs', 'options' ) as $option_file_name ) {
	require dirname( __FILE__ ) . '/../data/' . $option_file_name . '.php';
}

/**
 * Include core files
 */
foreach ( array( 'pages', 'options' ) as $core_file_name ) {
	include dirname( __FILE__ ) . '/core-' . $core_file_name . '.php';
}
