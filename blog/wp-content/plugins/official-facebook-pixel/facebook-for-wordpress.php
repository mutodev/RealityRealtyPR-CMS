<?php /**
 * Plugin Name: Facebook Pixel
 * Plugin URI: https://www.facebook.com/business/help/881403525362441
 * Description: The Facebook pixel is an analytics tool that helps you measure the effectiveness of your advertising. You can use the Facebook pixel to understand the actions people are taking on your website and reach audiences you care about.
 * Author: Facebook
 * Author URI: https://www.facebook.com/
 * Version: 1.7.9
 * Text Domain: official-facebook-pixel
 */
/**
 * @package FacebookPlugin
 */

defined('ABSPATH') or die('Direct access not allowed');

if (!class_exists('FacebookPixelPlugin')) :

if (!class_exists('FacebookPixel')) {
  include_once 'facebook-commerce-pixel-event.php';
}

if (!class_exists('FacebookPixelIntegration')) {
  include_once 'facebook-wordpress.php';
}

class FacebookPixelPlugin {

  // Change it above as well
  const PLUGIN_VERSION = '1.7.9';
  const TEXT_DOMAIN = 'official-facebook-pixel';

  public function __construct() {
    FacebookPixel::initialize();

    // Register WordPress integration.
    add_action('init', array($this, 'register_pixel_integration'), 0);
    $this->register_settings_page();
  }

  /**
   * Helper function for registering this integration.
   */
  public function register_pixel_integration() {
    return new FacebookPixelIntegration();
  }

  /**
   * Helper function for registering the settings page.
   */
  public function register_settings_page() {
    if (is_admin()) {
      if (!class_exists('FacebookPixelConfig')) {
        include_once 'facebook-wordpress-config.php';
      }
      $plugin_name = plugin_basename(__FILE__);
      new FacebookPixelConfig($plugin_name);
    }
  }
}

$WP_FacebookPixel = new FacebookPixelPlugin();

endif;
