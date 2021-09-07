<?php

require_once WC_ABSPATH . 'includes/wc-cart-functions.php';
include_once WC_ABSPATH . 'includes/wc-notice-functions.php';
require_once __ROOT__ . '/helpers/constants.php';

class WC_Sipay_ExpressCheckout_Helper
{

  public function getTagsFromCart(){
    $quote = WC()->cart;
    $has_virtual   = false;
    $has_novirtual = false;
    
    foreach($quote->get_cart() as $item => $values){
      if($values["data"]->is_virtual()){
        $has_virtual = true;
      }else{
        $has_novirtual = true;
      }
    }
    if ($has_virtual && $has_novirtual) {
      return CHECKOUT_TAG_BOTH;
    } else if ($has_virtual) {
      return CHECKOUT_TAG_VIRTUAL;
    } else {
      return CHECKOUT_TAG_NOVIRTUAL;
    }
  }

  public function getTagsFromOrder($order){
    $has_virtual   = false;
    $has_novirtual = false;
    
    foreach($order->get_items() as $item_product){
      if ($item_product->get_product()->is_virtual()) {
        $has_virtual = true;
      } else {
        $has_novirtual = true;
      }
    }

    if ($has_virtual && $has_novirtual) {
      return CHECKOUT_TAG_BOTH;
    } else if ($has_virtual) {
      return CHECKOUT_TAG_VIRTUAL;
    } else {
      return CHECKOUT_TAG_NOVIRTUAL;
    }
  }

  
}