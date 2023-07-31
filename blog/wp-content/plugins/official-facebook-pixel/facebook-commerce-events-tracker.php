<?php
/**
 * @package FacebookPlugin
 */

defined('ABSPATH') or die('Direct access not allowed');

if (!class_exists('FacebookPixelEvents')) :

if (!class_exists('FacebookPixel')) {
  include_once 'facebook-commerce-pixel-event.php';
}

class FacebookPixelEvents {
  private $pixel;

  public function __construct($user_info) {
    $this->pixel = new FacebookPixel($user_info);
  }

  /**
   * Base pixel code to be injected on page head.
   */
  public function inject_base_pixel() {
    echo $this->pixel->pixel_base_code();
  }

  /**
   * Base pixel noscript to be injected on page body. This is to avoid W3
   * validation error.
   */
  public function inject_base_pixel_noscript() {
    echo $this->pixel->pixel_base_code_noscript();
  }

  /**
   * Triggers Search for result pages (deduped)
   */
  public function inject_search_event() {
    if (!is_admin() && is_search() && get_search_query() !== '') {
      if ($this->pixel->check_last_event('Search')) {
        return;
      }

      $this->pixel->inject_event(
        'Search',
        array(
          'search_string' => get_search_query()
        ));
    }
  }

  public function inject_lead_event() {
    if (!is_admin()) {
      $this->pixel->inject_conditional_event(
        'Lead',
        array(),
        'wpcf7submit',
        '{ em: event.detail.inputs.filter(ele => ele.name.includes("email"))[0].value }');
    }
  }
}

endif;
