<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/valentinzamarin
 * @since             1.0.0
 * @package           Apf
 *
 * @wordpress-plugin
 * Plugin Name:       advanced post filter 
 * Plugin URI:        https://github.com/valentinzamarin/advanced-post-filter
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            zamarin 
 * Author URI:        https://github.com/valentinzamarin
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       apf
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'APF_VERSION', '1.0.0' );

define( 'APF_PLUGIN', __FILE__ );

define( 'APF_PLUGIN_DIR', untrailingslashit( dirname( APF_PLUGIN ) ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-apf-activator.php
 */
function activate_apf() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-apf-activator.php';
	Apf_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-apf-deactivator.php
 */
function deactivate_apf() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-apf-deactivator.php';
	Apf_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_apf' );
register_deactivation_hook( __FILE__, 'deactivate_apf' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-apf.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_apf() {

	$plugin = new Apf();
	$plugin->run();

}
run_apf();
