<?php

/**
* @file
* Main file of the "PressApps Plugin Framework"
* 
* The file loads the core files if not already done inside another plugin,
* then it loads the options defined in the options folder.
* 
* @package pressapps-admin-framework
*/

/**
 * Load the framework core if not done inside another plugin
 */
if ( ! defined( 'PAF' ) ) {
	include dirname( __FILE__ ) . '/core/core.php';
}

/**
 * Use sample data if $skelet_use_sample_data evaluates to true
 */
if ( K::get_var( 'skelet_use_sample_data' ) ) {
	foreach ( array( 'pages', 'tabs', 'sections', 'options' ) as $option_file_name ) {

		$option_file_path = dirname( __FILE__ ) . '/sample-data/' . $option_file_name . '.php';
		if ( file_exists( $option_file_path ) ) {
			require $option_file_path;
		}
	}
}
