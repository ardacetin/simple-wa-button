<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Custom meta box html
wp_nonce_field('swb_settings_meta_box', 'swb_settings_meta_box_nonce');
$post_id = get_the_ID();
$button_hide_status = get_post_meta($post_id, '_swb_button_hide_status', true);
?>
<p class="meta-options">
    <label for="swb_button_hide_status" class="selectit">
        <input name="swb_button_hide_status" type="checkbox" id="swb_button_hide_status" value="1" <?php checked('1', esc_attr($button_hide_status)); ?>>
        <?php echo esc_html__('Disable WhatsApp button on this page', 'simple-wa-button'); ?>
    </label>
</p>