<?php
/**
 * @package FacebookPlugin
 */

defined('ABSPATH') or die('Direct access not allowed');

if (!class_exists('FacebookPluginUtils')) :

/**
 * Helper functions
 */
class FacebookPluginUtils {

  const INTEGRATION_NAME = 'WordPress';

  /**
   * Returns user info for the current WP user.
   *
   * @access public
   * @param boolean $use_pii
   * @return array
   */
  public static function get_user_info($use_pii) {
    $current_user = wp_get_current_user();
    if (0 === $current_user->ID || $use_pii === false) {
      // User not logged in or admin chose not to send PII.
      return array();
    } else {
      return array_filter(
        array(
          // Keys documented in
          // https://developers.facebook.com/docs/facebook-pixel/pixel-with-ads/
          // /conversion-tracking#advanced_match
          'em' => $current_user->user_email,
          'fn' => $current_user->user_firstname,
          'ln' => $current_user->user_lastname
        ),
        function ($value) { return $value !== null && $value !== ''; });
    }
  }

  /**
   * Returns true if id is a positive non-zero integer
   *
   * @access public
   * @param string $pixel_id
   * @return bool
   */
  public static function is_positive_integer($pixel_id) {
    return isset($pixel_id) && is_numeric($pixel_id) && (int)$pixel_id > 0;
  }
}

endif;
