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
 * Include options files
 */
foreach ( array( 'pages' ) as $option_file_name ) {
	include dirname( __FILE__ ) . '/../data/' . $option_file_name . '.php';
}

/**
 * Include core files
 */
foreach ( array( 'pages' ) as $core_file_name ) {
	include dirname( __FILE__ ) . '/core-' . $core_file_name . '.php';
}
