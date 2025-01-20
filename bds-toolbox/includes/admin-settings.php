<?php
/**
 * Admin Settings for BDS Toolbox
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class BDSToolboxAdmin {
    
    const OPTION_NAME = 'bds_toolbox_custom_html';
    const SETTINGS_GROUP = 'bds_toolbox_settings_group';
    const MENU_SLUG = 'bds-toolbox';

    /**
     * Initialize admin settings.
     */
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_admin_menu']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
        add_action('plugins_loaded', [__CLASS__, 'load_textdomain']);
    }

    /**
     * Load plugin translations.
     */
    public static function load_textdomain() {
        load_plugin_textdomain('bds-toolbox', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * Add admin menu for the settings page.
     */
    public static function add_admin_menu() {
        add_options_page(
            esc_html__('BDS Toolbox Settings', 'bds-toolbox'), // Page title.
            esc_html__('BDS Toolbox', 'bds-toolbox'),          // Menu title.
            'manage_options',                                  // Capability required.
            self::MENU_SLUG,                                   // Menu slug.
            [__CLASS__, 'render_settings_page']                // Callback function.
        );
    }

    /**
     * Render the settings page.
     */
    public static function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('BDS Toolbox Settings', 'bds-toolbox'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields(self::SETTINGS_GROUP);
                do_settings_sections(self::MENU_SLUG);
                wp_nonce_field('bds_toolbox_settings_action', 'bds_toolbox_nonce'); // Nonce field for security
                submit_button(esc_html__('Save Settings', 'bds-toolbox'));
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register plugin settings.
     */
    public static function register_settings() {
        register_setting(
            self::SETTINGS_GROUP,
            self::OPTION_NAME,
            [
                'type'              => 'string',
                'sanitize_callback' => [__CLASS__, 'sanitize_custom_html'], // Ensure safe HTML
            ]
        );

        add_settings_section(
            'bds_toolbox_main_section',
            esc_html__('Custom HTML Content', 'bds-toolbox'),
            function () {
                echo '<p>' . esc_html__('Enter the HTML content to display to visitors from Israel. If left blank, the default BDS page will be displayed.', 'bds-toolbox') . '</p>';
            },
            self::MENU_SLUG
        );

        add_settings_field(
            'bds_toolbox_custom_html',
            esc_html__('Custom HTML', 'bds-toolbox'),
            [__CLASS__, 'custom_html_field_callback'],
            self::MENU_SLUG,
            'bds_toolbox_main_section'
        );
    }

    /**
     * Sanitize and validate custom HTML input.
     *
     * @param string $input User input from settings field.
     * @return string Sanitized and validated input.
     */
    public static function sanitize_custom_html($input) {
        // Ensure nonce exists before processing
        if (!isset($_POST['bds_toolbox_nonce'])) {
            return get_option(self::OPTION_NAME, ''); // Return previous value if nonce is missing
        }

        // Unslash and sanitize nonce before verification
        $nonce = sanitize_text_field(wp_unslash($_POST['bds_toolbox_nonce']));

        if (!wp_verify_nonce($nonce, 'bds_toolbox_settings_action')) {
            return get_option(self::OPTION_NAME, ''); // Return previous value if nonce validation fails
        }

        return wp_kses_post($input); // Sanitize the input, allowing only safe HTML
    }

    /**
     * Render the custom HTML field in the settings page.
     */
    public static function custom_html_field_callback() {
        $html = get_option(self::OPTION_NAME, '');
        echo '<textarea name="' . esc_attr(self::OPTION_NAME) . '" rows="10" cols="50" class="large-text">' . esc_textarea($html) . '</textarea>';
    }
}

// Initialize admin settings
BDSToolboxAdmin::init();
