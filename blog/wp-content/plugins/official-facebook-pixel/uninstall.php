<?php

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
  die;
}

include_once 'facebook-commerce-pixel-event.php';
include_once 'facebook-wordpress-config.php';

delete_option(FacebookPixel::SETTINGS_KEY);
delete_user_meta(get_current_user_id(), FacebookPixelConfig::IGNORE_PIXEL_ID_NOTICE);
