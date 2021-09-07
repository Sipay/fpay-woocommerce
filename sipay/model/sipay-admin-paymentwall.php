<?php

require_once __ROOT__.'/view/index.php';
require_once __ROOT__.'/helpers/index.php';

class WC_Sipay_Paymentwall extends WC_Payment_Gateway
{

  public function __construct(){
    $this->id                 = 'sipay_woocommerce';
    $this->method_title       = 'Fastpay';
    $this->method_description = 'Fastpay acepta múltiples formas de pago (Tarjetas Visa, MasterCard, AmericanExpress, UnionPayInternational, JCB, Discover y Dinners), Amazon Pay, PayPal, Google Pay, Apple Pay, Bizum, pago por transferencia y pago financiado a través del muro de pagos de Sipay. ¡Todos los métodos de pago con una sola integración que ayudan a los comercios a vender más!';
    $this->supports           = array('products');
    $this->title              = $this->get_option('method_title');
    $this->has_fields         = true;
    $this->form_fields        = sipay_admin_payment_config();

    $this->init_settings();

    $this->enabled                      = $this->get_option('enabled');
    $this->environment                  = $this->get_option('sipay_environment');
    $this->key                          = $this->get_option('sipay_key');
    $this->resource                     = $this->get_option('sipay_resource');
    $this->secret                       = $this->get_option('sipay_secret');

    $environment = $this->getEnvironmentUrl();

    //checkout resources
    add_action('wp_footer', function() use ($environment) {enqueue_styles($environment);});
    add_action('wp_footer', function() use($environment){checkout_enqueue_scripts($environment, $this->environment);});
    //admin resources
    add_action('admin_enqueue_scripts', function() use ($environment) {enqueue_styles($environment);});
    add_action('admin_enqueue_scripts', function() use($environment){enqueue_admin_scripts($environment);});
    add_action('admin_footer', function() use ($environment) {sipay_admin_paymentwall($this->environment);});
    add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this,'process_admin_options'));
  }

  private function getEnvironmentUrl(){
    if ($this->environment == 'sandbox') {
      return 'https://sandbox.sipay.es';
    }else if($this->environment == 'develop'){
      return 'https://develop.sipay.es';
    }
    return 'https://live.sipay.es';
  }

  public function payment_fields(){
    render_checkout_paymentwall($this->environment);
  }

  public function process_payment($order_id) {

  }
}
