<?php
/**
 * @package FacebookPlugin
 */

defined('ABSPATH') or die('Direct access not allowed');

if (!class_exists('FacebookPixelConfig')) :

class FacebookPixelConfig {
  const MENU_SLUG = 'facebook_pixel_options';
  const OPTION_GROUP = 'facebook_option_group';
  const SECTION_ID = 'facebook_settings_section';
  const IGNORE_PIXEL_ID_NOTICE = 'ignore_pixel_id_notice';
  const DISMISS_PIXEL_ID_NOTICE = 'dismiss_pixel_id_notice';

  private $options;
  private $options_page;

  public function __construct($plugin_name) {
    add_action('admin_menu', array($this, 'add_menu'));
    add_action('admin_init', array($this, 'register_settings'));
    add_action('current_screen', array($this, 'register_notices'));
    add_action('admin_init', array($this, 'dismiss_notices'));
    add_action('admin_enqueue_scripts', array($this, 'register_plugin_styles'));
    add_filter(
      'plugin_action_links_'.$plugin_name,
      array($this, 'add_settings_link'));
  }

  public function add_menu() {
    $this->options_page = add_options_page(
      'Facebook Pixel Settings',
      'Facebook Pixel',
      'manage_options',
      self::MENU_SLUG,
      array($this, 'create_menu_page'));
  }

  public function create_menu_page() {
    if (!current_user_can('manage_options')) {
      wp_die(__(
        'You do not have sufficient permissions to access this page',
        FacebookPixelPlugin::TEXT_DOMAIN));
    }
    // Update class field
    $this->options = FacebookPixel::get_options();

    ?>
    <div class="wrap">
      <h2>Facebook Pixel Settings</h2>
      <form action="options.php" method="POST">
        <?php
        settings_fields(self::OPTION_GROUP);
        do_settings_sections(self::MENU_SLUG);
        submit_button();
        ?>
      </form>
    </div>
    <?php
  }

  public function register_settings() {
    register_setting(
      self::OPTION_GROUP,
      FacebookPixel::SETTINGS_KEY,
      array($this, 'sanitize_input'));
    add_settings_section(
      self::SECTION_ID,
      null,
      null,
      self::MENU_SLUG);
    add_settings_field(
      FacebookPixel::PIXEL_ID_KEY,
      'Pixel ID',
      array($this, 'pixel_id_form_field'),
      self::MENU_SLUG,
      self::SECTION_ID);
    add_settings_field(
      FacebookPixel::USE_PII_KEY,
      'Use Advanced Matching on pixel?',
      array($this, 'use_pii_form_field'),
      self::MENU_SLUG,
      self::SECTION_ID);
  }

  public function sanitize_input($input) {
    $input[FacebookPixel::USE_PII_KEY] =
      isset($input[FacebookPixel::USE_PII_KEY]) &&
      $input[FacebookPixel::USE_PII_KEY] == 1
        ? '1'
        : '0';
    $input[FacebookPixel::PIXEL_ID_KEY] =
      isset($input[FacebookPixel::PIXEL_ID_KEY])
        ? FacebookPluginUtils::is_positive_integer($input[FacebookPixel::PIXEL_ID_KEY])
          ? $input[FacebookPixel::PIXEL_ID_KEY]
          : ''
        : FacebookPixel::get_pixel_id();

    return $input;
  }

  public function pixel_id_form_field() {
    $description = esc_html__(
      'The unique identifier for your Facebook pixel.',
      FacebookPixelPlugin::TEXT_DOMAIN);
    printf(
      '
<input name="%s" id="%s" value="%s" />
<p class="description">%s</p>
    ',
      FacebookPixel::SETTINGS_KEY . '[' . FacebookPixel::PIXEL_ID_KEY . ']',
      FacebookPixel::PIXEL_ID_KEY,
      isset($this->options[FacebookPixel::PIXEL_ID_KEY])
        ? esc_attr($this->options[FacebookPixel::PIXEL_ID_KEY])
        : '',
      $description);
  }

  public function use_pii_form_field() {
    $url = "https://developers.facebook.com/docs/privacy/";
    $link = sprintf(
      wp_kses(
        __(
          'For businesses that operate in the European Union, you may need to take additional action. Read the <a href="%s" target="_blank">Cookie Consent Guide for Sites and Apps</a> for suggestions on complying with EU privacy requirements.',
          FacebookPixelPlugin::TEXT_DOMAIN),
        array('a' => array('href' => array(), 'target' => array()))),
      esc_url($url));
    ?>
    <label for="<?= FacebookPixel::USE_PII_KEY ?>">
      <input
        type="checkbox"
        name="<?= FacebookPixel::SETTINGS_KEY . '[' . FacebookPixel::USE_PII_KEY . ']' ?>"
        id="<?= FacebookPixel::USE_PII_KEY ?>"
        value="1"
        <?php checked(1, $this->options[FacebookPixel::USE_PII_KEY]) ?>
      />
      <?= esc_html__(
        'Enabling Advanced Matching improves audience building.',
        FacebookPixelPlugin::TEXT_DOMAIN); ?>
    </label>
    <p class="description">
      <?= $link ?>
    </p>
    <?php
  }

  public function register_notices() {
    // Update class field
    $this->options = get_option(FacebookPixel::SETTINGS_KEY);
    $pixel_id = $this->options[FacebookPixel::PIXEL_ID_KEY];
    $current_screen_id = get_current_screen()->id;
    if (
      !FacebookPluginUtils::is_positive_integer($pixel_id)
      && current_user_can('manage_options')
      && in_array(
        $current_screen_id,
        array('dashboard', 'plugins', $this->options_page),
        true)
      && !get_user_meta(
        get_current_user_id(),
        self::IGNORE_PIXEL_ID_NOTICE,
        true)) {
      add_action('admin_notices', array($this, 'pixel_id_not_set_notice'));
    }
  }

  public function dismiss_notices() {
    $user_id = get_current_user_id();
    if (isset($_GET[self::DISMISS_PIXEL_ID_NOTICE])) {
      update_user_meta($user_id, self::IGNORE_PIXEL_ID_NOTICE, true);
    }
  }

  public function pixel_id_not_set_notice() {
    $url = admin_url('options-general.php?page='.self::MENU_SLUG);
    $link = sprintf(
      wp_kses(
        __(
          'The Facebook Pixel plugin requires a Pixel ID. Click <a href="%s">here</a> to configure the plugin.',
          FacebookPixelPlugin::TEXT_DOMAIN),
        array('a' => array('href' => array()))),
      esc_url($url));
    ?>
    <div class="notice notice-warning is-dismissible hide-last-button">
      <p>
        <?= $link ?>
      </p>
      <button
        type="button"
        class="notice-dismiss"
        onClick="location.href='<?= esc_url(add_query_arg(self::DISMISS_PIXEL_ID_NOTICE, '')) ?>'">
        <span class="screen-reader-text">Dismiss this notice.</span>
      </button>
    </div>
    <?php
  }

  public function register_plugin_styles() {
    wp_register_style(
      'official-facebook-pixel',
      plugins_url('css/admin.css', __FILE__));
    wp_enqueue_style('official-facebook-pixel');
  }

  public function add_settings_link($links) {
    $settings = array(
      'settings' => sprintf(
        '<a href="%s">%s</a>',
        admin_url('options-general.php?page='.self::MENU_SLUG),
        'Settings')
    );
    return array_merge($settings, $links);
  }
}

endif;
