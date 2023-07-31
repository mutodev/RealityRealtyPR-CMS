<?php
/**
 * @package FacebookPlugin
 */

defined('ABSPATH') or die('Direct access not allowed');

if (!class_exists('FacebookPixelIntegration')) :

if (!class_exists('FacebookPixelEvents')) {
  include_once 'facebook-commerce-events-tracker.php';
}

if (!class_exists('FacebookPluginUtils')) {
  include_once 'includes/fbutils.php';
}

class FacebookPixelIntegration {
  private $events_tracker;

  public function __construct() {
    $options = get_option(FacebookPixel::SETTINGS_KEY);
    $pixel_id = $options[FacebookPixel::PIXEL_ID_KEY];
    $use_pii = $options[FacebookPixel::USE_PII_KEY];

    if (FacebookPluginUtils::is_positive_integer($pixel_id)) {
      $user_info = FacebookPluginUtils::get_user_info($use_pii == '1');
      $this->events_tracker = new FacebookPixelEvents($user_info);

      // Pixel Tracking Hooks
      add_action('wp_head',
        array($this->events_tracker, 'inject_base_pixel'));
      add_action('wp_head',
        array($this->events_tracker, 'inject_base_pixel_noscript'));
      add_action('wp_head',
        array($this->events_tracker, 'inject_search_event'), 11);
      add_action('wpcf7_contact_form',
        array($this, 'inject_lead_event_hook'), 11);
    }
  }

  public function inject_lead_event_hook() {
      add_action('wp_footer',
        array($this->events_tracker, 'inject_lead_event'), 11);
  }

  /**
   * Helper log function for debugging
   *
   * @since 1.2.2
   */
  public static function log($message) {
    if (WP_DEBUG === true) {
      if (is_array($message) || is_object($message)) {
        error_log(json_encode($message));
      }
      else {
        error_log($message);
      }
    }
  }
}

endif;
