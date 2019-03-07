<?php

/**
* @file
* Main file of the "Opt Framework"
* 
* The file loads the core files if not already done inside another plugin,
* then it loads the options defined in the options folder.
* 
* @package opt
*/

/**
 * Load the framework core if not done inside another plugin
 */
if ( ! defined( 'OPT' ) ) {
	include dirname( __FILE__ ) . '/core/core.php';
}

/**
 * Use sample data if $opt_use_sample_data evaluates to true
 */
if ( K::get_var( 'opt_use_sample_data' ) ) {
	opt_dir( dirname( __FILE__ ) . '/sample-data/' );
}
