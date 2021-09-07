<?php

require_once __ROOT__ . '/helpers/constants.php';

add_action('woocommerce_proceed_to_checkout', function () {
  if (WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[cart_enabled]') == "no") {
    return;
  }
  $environment = WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_settings[sipay_environment]');
  $enabled_container_style = WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[cart_container_customization]');
  $enabled_position = WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[cart_position_mode]');

  $logo_id = get_theme_mod( 'custom_logo' );

  wp_localize_script('sipay-expresscheckout', 'sipay_ec_config', [
    "enviroment" => $environment,
    "backendUrl" => get_rest_url(null, '/sipay/v1/actions'),
    "profile" => "woocommerce_cart",
    "quoteInfoUrl" => get_rest_url(null, '/sipay/v1/quote-ec'),
    "containerStyle" => $enabled_container_style === 'yes' ? [
      "color" => WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[cart_container_border_color]'),
      "custom_color" => WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[cart_container_custom_color]'),
      "header_title" =>  WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[cart_container_header_title]'),
      "header_title_typo" =>  WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[cart_container_header_font]'),
      "descriptive_text" =>  WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[cart_container_descriptive_text]'),
      "descriptive_text_typo" =>  WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[cart_container_descriptive_font]')
    ] : [],
    "positionConfig" => $enabled_position === '1' ? [
      "insertion" => WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[cart_position_insertion]'),
      "position_selector" => WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[cart_position_selector]')
    ] : [],
    "positionStyleConfig" => $enabled_position === '1' ? WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[cart_position_style]') : "",
    "storeLogoUrl" => $logo_id ? wp_get_attachment_image_src($logo_id, 'full')[0] : null,
    "element" => $enabled_position === '1' ? WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[cart_position_selector]') : "#sipay-cart-app"
  ]);
?>
  <div id="sipay-cart-app"></div>
<?php
});
