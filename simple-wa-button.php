<?php
/*
 * Plugin Name: Simple WA Button
 * Description: Adds a WhatsApp Sticky Button on the WordPress.
 * Author:      Arda Çetin
 * Author URI:  https://ardacetin.org
 * Version:     1.0
 * License:     GPLv2
 * Text Domain: simple-wa-button
 * Domain Path: /languages/
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('SWB_VERSION', '1.0.0');
define('SWB_NAME', plugin_basename(__FILE__));
define('SWB_DIR', plugin_dir_path(__FILE__));
define('SWB_URI', plugin_dir_url(__FILE__));
define('SWB_INC', trailingslashit(SWB_DIR . 'includes'));

// Check main class exists
if (!class_exists('SWB_Main')) {

    // Include whatsapp whatsapp button frontend
    include SWB_INC . 'custom_functions.php';
    include SWB_INC . 'frontend.php';

    // Define main class
    class SWB_Main {

        public function __construct() {
            // Add settings page
            add_action('admin_menu', array($this, 'swb_settings_page'));

            // Register settings
            add_action('admin_init', array($this, 'swb_register_settings'));

            // Add custom meta box
            add_action('add_meta_boxes', array($this,'swb_add_custom_meta_box'));

            // Add save custom meta box data
            add_action('save_post', array($this, 'swb_save_post_data'));

            // Add settings link
            add_filter('plugin_action_links_' . SWB_NAME, array($this, 'swb_add_settings_link'));

            // Loads translated strings
            load_plugin_textdomain('simple-wa-button', false, dirname(SWB_NAME) . '/languages');
        }

        // Add setting page to options submenu
        function swb_settings_page() {
            add_submenu_page(
                'options-general.php',
                esc_html__('Simple WA Button', 'simple-wa-button'),
                esc_html__('Simple WA Button', 'simple-wa-button'),
                'manage_options',
                'simple-wa-button',
                array($this, 'swb_settings_page_callback')
            );
        }

        // Register settings
        function swb_register_settings() {
			$swb_settings_args = array (
				'sanitize_callback' => 'sanitize_text_field'
			);
            $swb_whatsapp_options = array(
                'swb_whatsapp_number',
                'swb_whatsapp_whatsapp_text'
            );
            $swb_button_options = array(
                'swb_button_status',
                'swb_button_text',
                'swb_button_target',
                'swb_button_position',
                'swb_button_z_index',
                'swb_desktop_link_type',
                'swb_desktop_bottom_margin',
                'swb_tablet_bottom_margin',
                'swb_mobile_bottom_margin'
            );
            foreach ($swb_whatsapp_options as $option) {
                register_setting('scb-whatsapp-settings', $option, $swb_settings_args);
            }
            foreach ($swb_button_options as $option) {
                register_setting('scb-button-settings', $option, $swb_settings_args);
            }
            // Initialize options
            add_option('swb_whatsapp_whatsapp_text', esc_html__('Hello', 'simple-wa-button'));
            add_option('swb_button_status', '1');
            add_option('swb_button_text', esc_html__('Need Help?', 'simple-wa-button'));
            add_option('swb_button_target', '_blank');
            add_option('swb_button_position', 'right');
            add_option('swb_desktop_link_type', 'api');
            add_option('swb_desktop_bottom_margin', '20');
            add_option('swb_tablet_bottom_margin', '20');
            add_option('swb_mobile_bottom_margin', '20');
        }

        // Settings page callback
        function swb_settings_page_callback() {
            // Check access
            if (!current_user_can("manage_options") && !is_admin()) {
                return;
            }
            // Include settings page
            include SWB_INC . 'settings-page.php';
        }

        // Add custom meta box
        function swb_add_custom_meta_box() {
            $screens = array(
                'post',
                'page',
            );
            foreach ($screens as $screen) {
                add_meta_box(
                    'swb_custom_meta_box',
                    esc_html__('Simple WA Button Settings', 'simple-wa-button'),
                    array($this, 'swb_custom_meta_box_callback'),
                    $screen,
                    'normal',
                    'default',
                );
            }
        }

        // Custom meta box html
        function swb_custom_meta_box_callback($post) {
            // Check access
            if (!current_user_can("manage_options") && !is_admin()) {
                return;
            }
            // Include meta box html
            include SWB_INC . 'meta-box.php';
        }

        // Save custom meta box data
        function swb_save_post_data($post_id){
            // Check plugin nonce is set
            if (!isset($_POST['swb_settings_meta_box_nonce'])) {
                return $post_id;
            }

            // Verify that the nonce is valid
            $nonce = sanitize_text_field($_POST['swb_settings_meta_box_nonce']);
            if (!wp_verify_nonce($nonce, 'swb_settings_meta_box')) {
                return $post_id;
            }

            // Check auto save form
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return $post_id;
            }

            // Check the user permissions
            if ('page' == $_POST['post_type']) {
                if (!current_user_can('edit_page', $post_id)) {
                    return $post_id;
                }
            } else {
                if (!current_user_can('edit_post', $post_id)) {
                    return $post_id;
                }
            }

            // Sanitize the user input and save post meta
            $button_hide_status = sanitize_text_field($_POST['swb_button_hide_status']);
            if (!empty($button_hide_status)) {
                update_post_meta($post_id, '_swb_button_hide_status', $button_hide_status);
            } else {
                delete_post_meta($post_id, '_swb_button_hide_status');
            }
        }

        // Add settings link
        function swb_add_settings_link($links) {
            $links[] = sprintf('<a href="%1$s">%2$s</a>', admin_url('options-general.php?page=simple-wa-button'), esc_html__('Settings', 'simple-wa-button'));
            $links[] = sprintf('<a href="https://ardacetin.org">%1$s</a>', esc_html__('Website', 'simple-wa-button'));
            return $links;
        }

    }

    new SWB_Main();
}