<?php
require_once WC_ABSPATH . 'includes/wc-cart-functions.php';

class WC_Sipay_Quote
{

  protected $environment;

  public function __construct(){
    $this->sipay_settings = get_option('woocommerce_sipay_woocommerce_settings');
    $this->environment    = $this->sipay_settings["sipay_environment"];
    $this->checkout_helper = new WC_Sipay_Checkout_Helper();
  }

  public function getQuoteInfo(){
    $quoteData = [];
    if(WC()->cart === null){
        $this->checkout_helper->loadFromSession();
    }
    $quote = WC()->cart;

    $quoteAmount  = $quote ? wc_format_decimal($quote->total, wc_get_price_decimals()) : "0";

    //$customerId   = WC()->customer ? WC()->customer->id : "0";

    $quoteData["groupId"]     = "0";
    $quoteData["amount"]      = $quoteAmount;
    $quoteData["currency"]    = get_woocommerce_currency();

    return $quoteData;
  }
}
