<?php
/**
 * Admin Settings for BDS Toolbox
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Add admin menu for the settings page.
add_action( 'admin_menu', 'bds_toolbox_add_admin_menu' );
function bds_toolbox_add_admin_menu() {
    add_options_page(
        esc_html__( 'BDS Toolbox Settings', 'bds-toolbox' ), // Page title.
        esc_html__( 'BDS Toolbox', 'bds-toolbox' ),          // Menu title.
        'manage_options',                                   // Capability required.
        'bds-toolbox',                                      // Menu slug.
        'bds_toolbox_settings_page'                         // Callback function to render the page.
    );
}

// Render the settings page content.
function bds_toolbox_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'BDS Toolbox Settings', 'bds-toolbox' ); ?></h1>
        <form method="post" action="options.php">
            <?php
            // Output the settings fields and sections for this plugin.
            settings_fields( 'bds_toolbox_settings_group' );
            do_settings_sections( 'bds-toolbox' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register plugin settings.
add_action( 'admin_init', 'bds_toolbox_register_settings' );
function bds_toolbox_register_settings() {
    // Register the setting to store custom HTML content.
    register_setting(
        'bds_toolbox_settings_group', // Option group.
        'bds_toolbox_custom_html',    // Option name.
        [
            'type'              => 'string',
            'sanitize_callback' => 'wp_kses_post', // Ensure the HTML content is safe.
        ]
    );

    // Add a settings section for custom HTML.
    add_settings_section(
        'bds_toolbox_main_section',                    // Section ID.
        esc_html__( 'Custom HTML Content', 'bds-toolbox' ), // Section title.
        function () {
            echo '<p>' . esc_html__( 'Enter the HTML content to display to visitors from Israel. If left blank, the default BDS page will be displayed.', 'bds-toolbox' ) . '</p>';
        },
        'bds-toolbox' // Page slug.
    );

    // Add a settings field for the custom HTML.
    add_settings_field(
        'bds_toolbox_custom_html',                    // Field ID.
        esc_html__( 'Custom HTML', 'bds-toolbox' ),   // Field title.
        'bds_toolbox_custom_html_field_callback',     // Callback function to render the field.
        'bds-toolbox',                                // Page slug.
        'bds_toolbox_main_section'                    // Section ID.
    );
}

// Render the custom HTML field in the settings page.
function bds_toolbox_custom_html_field_callback() {
    $html = get_option( 'bds_toolbox_custom_html', '' ); // Get the current value of the custom HTML option.
    echo '<textarea name="bds_toolbox_custom_html" rows="10" cols="50" class="large-text">' . esc_textarea( $html ) . '</textarea>';
}
