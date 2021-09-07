<?php

require_once __ROOT__ . '/helpers/constants.php';

add_action('woocommerce_widget_shopping_cart_before_buttons', function () {
  if (WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[minicart_enabled]') == "no") {
    return;
  }
  $environment = WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_settings[sipay_environment]');
  $enabled_container_style = WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[minicart_container_customization]');
  $enabled_position = WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[minicart_position_mode]');

  $logo_id = get_theme_mod('custom_logo');

  $containerStyle = $enabled_container_style === 'yes' ? [
    "color" => WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[minicart_container_border_color]'),
    "custom_color" => WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[minicart_container_custom_color]'),
    "header_title" =>  WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[minicart_container_header_title]'),
    "header_title_typo" =>  WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[minicart_container_header_font]'),
    "descriptive_text" =>  WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[minicart_container_descriptive_text]'),
    "descriptive_text_typo" =>  WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[minicart_container_descriptive_font]')
  ] : [];
  $positionConfig = $enabled_position === '1' ? [
    "insertion" => WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[minicart_position_insertion]'),
    "position_selector" => WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[minicart_position_selector]')
  ] : [];

?>

  <div id="sipay-minicart-app"></div>
  <script>
    window.sipay_ec_minicart_config = {
      enviroment: "<?php echo $environment ?>",
      backendUrl: "<?php echo get_rest_url(null, '/sipay/v1/actions') ?>",
      profile: "woocommerce_minicart",
      quoteInfoUrl: "<?php echo get_rest_url(null, '/sipay/v1/quote-ec') ?>",
      containerStyle: JSON.parse('<?php echo json_encode($containerStyle) ?>'),
      positionConfig: JSON.parse('<?php echo json_encode($positionConfig) ?>'),
      positionStyleConfig: "<?php echo $enabled_position === '1' ? WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[minicart_position_style]') : "" ?>",
      storeLogoUrl: "<?php echo $logo_id ? wp_get_attachment_image_src($logo_id, 'full')[0] : null ?>",
      element: "<?php echo $enabled_position === '1' ? WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[minicart_position_selector]') : "#sipay-minicart-app" ?>"
    };
    window.setMinicartListeners();
  </script>
<?php
});
