<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/anjakammer/data2table
 * @since             1.0.0
 * @package           D2T
 *
 * @wordpress-plugin
 * Plugin Name:       Data2Table
 * Plugin URI:        https://github.com/anjakammer/data2table/
 * Description:       A Wordpress-Plugin for creating database tables and handling CRUD operations
 * Version:           1.0.0
 * Author:            Anja Kammer
 * Author URI:        https://github.com/anjakammer/data2table/
 * Text Domain:       d2t
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/activator.php
 */
function activate_d2t() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/activator.php';
	D2T_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/deactivator.php
 */
function deactivate_d2t() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/deactivator.php';
	D2T_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_d2t' );
register_deactivation_hook( __FILE__, 'deactivate_d2t' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/d2t-class.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_d2t() {

	$plugin = new D2T();
	$plugin->run();
}
run_d2t();
