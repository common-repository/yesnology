<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress or ClassicPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://yesnology.com
 * @since             1.0.0
 * @package           YesNology
 *
 * @wordpress-plugin
 * Plugin Name:       YesNology
 * Plugin URI:        https://yesnology.com/en/how-does-it-work/
 * Description:       Through the plugin for YesNology it is possible to store the data entered by the user in a safe and reliable place 100% compliant with the GDPR. The plugin can be used for an unlimited number of collectors: collect newsletter subscriptions, customer satisfaction forms, customer surveys, ... and much more!!! Through a short code you can collect all the information you deem appropriate. Through the YesNology backend you will be able to consult the data collected and you will be able to use them in a form compliant with the GDPR. You will also be able to share the collected data with your CRM using the APIs that YesNology makes available to you. To use the plugin you need to have a subscription to YesNology. You can find more information at https://yesnology.com. You can contact us at info@yesnology.com
 * Version:           1.0.0
 * Author:            BBUp srl
 * Requires at least: 4.0.0
 * Requires PHP:      7.0.0
 * Tested up to:      6.0.0
 * Author URI:        https://yesnology.com/en/abous-us/
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       YesNology
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'YESNOLOGY_VERSION', '1.0.0' );

/**
 * Define the Plugin basename
 */
define( 'YESNOLOGY_BASE_NAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 *
 * This action is documented in includes/class-yesnology-activator.php
 * Full security checks are performed inside the class.
 */
function ynlgy_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-yesnology-activator.php';
	Yesnology_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 *
 * This action is documented in includes/class-yesnology-deactivator.php
 * Full security checks are performed inside the class.
 */
function ynlgy_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-yesnology-deactivator.php';
	Yesnology_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'ynlgy_activate' );
register_deactivation_hook( __FILE__, 'ynlgy_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-yesnology.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * Generally you will want to hook this function, instead of callign it globally.
 * However since the purpose of your plugin is not known until you write it, we include the function globally.
 *
 * @since    1.0.0
 */
function ynlgy_run() {

	$plugin = new Yesnology();
	$plugin->run();

}
ynlgy_run();
