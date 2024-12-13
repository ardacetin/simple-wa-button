<?php
// Die if uninstall file is not called by WordPress
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// Delete options
$swb_options = array(
	'swb_whatsapp_number',
	'swb_whatsapp_whatsapp_text',
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
foreach ($swb_options as $option) {
    if (get_option($option)) {
        delete_option($option);
    }
}