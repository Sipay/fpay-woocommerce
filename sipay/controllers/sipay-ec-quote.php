<?php
require_once WC_ABSPATH . 'includes/wc-cart-functions.php';
require_once __ROOT__ . '/helpers/constants.php';


class WC_Sipay_Ec_Quote
{

  protected $environment;

  public function __construct(){
    $this->sipay_settings = get_option('woocommerce_sipay_woocommerce_settings');
    $this->environment    = $this->sipay_settings["sipay_environment"];
    $this->checkout_helper = new WC_Sipay_Checkout_Helper();
  }

  public function getQuoteInfo(){
    $response = [];
    if(WC()->cart === null){
        $this->checkout_helper->loadFromSession();
    }
    $quote = WC()->cart;

    $response["tags"]        = EXPRESS_CHECKOUT_TAG;
    $response["currency"]    = get_woocommerce_currency();
    $response["amount"]      = $quote ? $quote->total : "0";
    $response["groupId"]     = WC()->customer ? strval(WC()->customer->get_id()) : "0";

    return $response;
  }
}
