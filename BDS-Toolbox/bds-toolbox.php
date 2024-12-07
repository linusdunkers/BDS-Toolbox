<?php
/**
 * Plugin Name: BDS Toolbox
 * Plugin URI: https://github.com/linusdunkers/BDS-Toolbox
 * Description: A WordPress plugin that filters visitors based on GeoIP location, showing a custom or default BDS page for visitors from Israel.
 * Version: 1.0
 * Author: Linus Dunkers
 * Author URI: https://github.com/linusdunkers
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: BDS-Toolbox
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define constants.
define( 'BDS_TOOLBOX_VERSION', '1.0' );
define( 'BDS_TOOLBOX_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BDS_TOOLBOX_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BDS_TOOLBOX_GEOIP_DB', BDS_TOOLBOX_PLUGIN_DIR . 'GeoLite2-Country.mmdb' );

// Include Composer autoloader for GeoIP2 library.
if ( file_exists( BDS_TOOLBOX_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
    require_once BDS_TOOLBOX_PLUGIN_DIR . 'vendor/autoload.php';
} else {
    return;
}

use GeoIp2\Database\Reader;

// Include admin settings file.
if ( file_exists( BDS_TOOLBOX_PLUGIN_DIR . 'includes/admin-settings.php' ) ) {
    require_once BDS_TOOLBOX_PLUGIN_DIR . 'includes/admin-settings.php';
} else {
    return;
}

// Activation hook.
register_activation_hook( __FILE__, 'bds_toolbox_activate' );
function bds_toolbox_activate() {
    if ( ! get_option( 'bds_toolbox_custom_html' ) ) {
        $default_html = '<h1>Access Restricted</h1><p>This content is not available in your location.</p>';
        update_option( 'bds_toolbox_custom_html', $default_html );
    }
}

// Deactivation hook.
register_deactivation_hook( __FILE__, 'bds_toolbox_deactivate' );
function bds_toolbox_deactivate() {
    // Cleanup tasks (if needed).
}

// Add GeoIP check to front-end requests.
add_action( 'template_redirect', 'bds_toolbox_geoip_filter' );

function bds_toolbox_geoip_filter() {
    // Define the path to the GeoIP database.
    if ( ! defined( 'BDS_TOOLBOX_GEOIP_DB' ) || ! file_exists( BDS_TOOLBOX_GEOIP_DB ) ) {
        return; // Skip GeoIP check if the database is missing or undefined.
    }

    // Validate and sanitize the visitor's IP address.
    $visitor_ip = '';
    if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
        $raw_ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ); // Unslash and sanitize the input.
        $visitor_ip = filter_var( $raw_ip, FILTER_VALIDATE_IP );
    }

    // Skip processing if the IP address is invalid.
    if ( ! $visitor_ip ) {
        return;
    }

    try {
        // Initialize GeoIP2 Reader.
        $reader = new GeoIp2\Database\Reader( BDS_TOOLBOX_GEOIP_DB );
        $record = $reader->country( $visitor_ip );
        $country_code = $record->country->isoCode;

        // Check if the visitor is from Israel (ISO code: IL).
        if ( $country_code === 'IL' ) {
            // Retrieve custom HTML content from the database.
            $custom_html = get_option( 'bds_toolbox_custom_html', '' );

            // Display the custom content or a default message if not set.
            if ( $custom_html ) {
                echo wp_kses_post( $custom_html );
            } else {
                echo '<h1>BDS Standard Page</h1><p>This is the standard BDS content.</p>';
            }

            exit; // Stop further processing.
        }
    } catch ( Exception $e ) {
        // Handle GeoIP errors gracefully.
        // Optionally log the error using error_log if debugging is required:
        // error_log( 'GeoIP error: ' . $e->getMessage() );
    }
}
