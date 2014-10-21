<?php

/**
* Main file of the "Plugin Options" framework
* 
* The file loads the core files if not already done inside another plugin,
* then it loads the options defined in the options folder.
* 
* @package plugin-admin-framework
*/

/**
 * Load the framework core if not done inside another plugin
 */
if ( ! defined( 'PLUGIN_OPTIONS' ) ) {
	include dirname( __FILE__ ) . '/core/core.php';
}

/**
 * Include options files
 */
foreach ( array( 'pages', 'tabs', 'options' ) as $option_file_name ) {

	require dirname( __FILE__ ) . '/data/' . $option_file_name . '.php';
}
