<?php
/**
 * Plugin Name:       BDS Toolbox
 * Plugin URI:        https://github.com/linusdunkers/BDS-Toolbox
 * Description:       A WordPress plugin that filters visitors based on GeoIP location, showing a custom or default BDS page for visitors from Israel.
 * Version:           1.0.2
 * Requires at least: 4.0
 * Requires PHP:      7.4
 * Author:            Linus Dunkers
 * Author URI:        https://github.com/linusdunkers
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       bds-toolbox
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class BDSToolbox {
    
    const VERSION = '1.0.2';
    const OPTION_NAME = 'bds_toolbox_custom_html';

    private static $geoip_db_path;

    /**
     * Initialize plugin constants, hooks, and actions.
     */
    public static function init() {
        define('BDS_TOOLBOX_PLUGIN_DIR', plugin_dir_path(__FILE__));
        define('BDS_TOOLBOX_PLUGIN_URL', plugin_dir_url(__FILE__));

        self::$geoip_db_path = BDS_TOOLBOX_PLUGIN_DIR . 'GeoLite2-Country.mmdb';

        self::load_dependencies();

        // Load translations
        add_action('plugins_loaded', [__CLASS__, 'load_textdomain']);

        register_activation_hook(__FILE__, [__CLASS__, 'activate']);
        register_deactivation_hook(__FILE__, [__CLASS__, 'deactivate']);

        add_action('template_redirect', [__CLASS__, 'geoip_filter']);
    }

    /**
     * Load necessary dependencies.
     */
    private static function load_dependencies() {
        if (file_exists(BDS_TOOLBOX_PLUGIN_DIR . 'vendor/autoload.php')) {
            require_once BDS_TOOLBOX_PLUGIN_DIR . 'vendor/autoload.php';
        } else {
            do_action('bds_toolbox_error', __('BDS Toolbox: Missing vendor/autoload.php', 'bds-toolbox'));
            return;
        }

        if (file_exists(BDS_TOOLBOX_PLUGIN_DIR . 'includes/admin-settings.php')) {
            require_once BDS_TOOLBOX_PLUGIN_DIR . 'includes/admin-settings.php';
        } else {
            do_action('bds_toolbox_error', __('BDS Toolbox: Missing admin-settings.php', 'bds-toolbox'));
            return;
        }
    }

    /**
     * Load plugin translations.
     */
    public static function load_textdomain() {
        load_plugin_textdomain('bds-toolbox', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * Plugin activation hook.
     */
    public static function activate() {
        if (!get_option(self::OPTION_NAME)) {
            $default_html = '<h1>' . esc_html__('Access Restricted', 'bds-toolbox') . '</h1><p>' . esc_html__('This content is not available in your location.', 'bds-toolbox') . '</p>';
            update_option(self::OPTION_NAME, $default_html);
        }
    }

    /**
     * Plugin deactivation hook.
     */
    public static function deactivate() {
        // Cleanup tasks if needed.
    }

    /**
     * GeoIP filter for visitors.
     */
    public static function geoip_filter() {
        if (!file_exists(self::$geoip_db_path)) {
            do_action('bds_toolbox_error', __('BDS Toolbox: GeoIP database missing.', 'bds-toolbox'));
            return;
        }

        $visitor_ip = '';
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            // Unslash and sanitize IP before filtering
            $raw_ip = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
            $visitor_ip = filter_var($raw_ip, FILTER_VALIDATE_IP);
        }

        if (!$visitor_ip) {
            return;
        }

        try {
            $reader = new GeoIp2\Database\Reader(self::$geoip_db_path);
            $record = $reader->country($visitor_ip);
            $country_code = $record->country->isoCode;

            if ($country_code === 'IL') {
                $custom_html = get_option(self::OPTION_NAME, '');

                if ($custom_html) {
                    echo wp_kses_post($custom_html);
                } else {
                    echo '<h1>' . esc_html__('BDS Standard Page', 'bds-toolbox') . '</h1><p>' . esc_html__('This is the standard BDS content.', 'bds-toolbox') . '</p>';
                }

                exit;
            }
        } catch (Exception $e) {
            do_action('bds_toolbox_error', __('BDS Toolbox GeoIP Error:', 'bds-toolbox') . ' ' . esc_html($e->getMessage()));
        }
    }
}

// Initialize the plugin.
BDSToolbox::init();
