<?php

require_once __ROOT__.'/view/index.php';
require_once __ROOT__.'/helpers/index.php';

class WC_Sipay_ExpressCheckout extends WC_Payment_Gateway
{

  public function __construct(){
    $this->id                 = 'sipay_woocommerce_ec';
    $this->method_title       = 'Sipay: Payment Wall Express Checkout';
    $this->method_description = 'Select the sections of your store and the express payment methods than you want to enable.';
    $this->supports           = array('products');
    $this->title              = $this->get_option('method_title');
    $this->has_fields         = true;
    $this->form_fields        = sipay_ec_admin_payment_config();

    $this->init_settings();

    $this->enabled                = $this->get_option('enabled');
    $environment                  = WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_settings[sipay_environment]');
    $environment_url              = $this->getEnvironmentUrl($environment);

    // //checkout resources
    // add_action('wp_footer', function() use ($environment) {enqueue_styles($environment);});
    // add_action('wp_footer', function() use($environment){checkout_enqueue_scripts($environment, $this->environment);});
    //admin resources
    add_action('admin_enqueue_scripts', function() use ($environment_url) {enqueue_styles($environment_url);});
    add_action('admin_enqueue_scripts', function() use($environment_url){enqueue_admin_scripts($environment_url);});
    add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this,'process_admin_options'));
    add_action('admin_enqueue_scripts', function() use($environment){
      wp_enqueue_style('sipay-collapsible-css',plugins_url('sipay/view/admin/css/express-checkout-admin.css'));
      wp_enqueue_script('sipay-expresscheckout-admin', plugins_url('sipay/view/admin/js/admin-expresscheckout.js'), array('jquery'));
      wp_localize_script('sipay-expresscheckout-admin', 'sipay_ec_admin', [
        "environment" => $environment,
        "backendUrl" => get_rest_url(null, '/sipay/v1/actions'),
        "custom_color_error_message" => __('Value is not valid, example #F1F1F1')
      ]);
    });
  }

  private function getEnvironmentUrl($environment){
    if ($environment == 'sandbox') {
      return 'https://sandbox.sipay.es';
    }else if($environment == 'develop'){
      return 'https://develop.sipay.es';
    }
    return 'https://live.sipay.es';
  }

  public function is_available()
  {
    if(is_checkout()){ //We dont want to be shown in checkout
      return false;
    }
   return true; 
  } 
}
