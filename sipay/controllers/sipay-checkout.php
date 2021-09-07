<?php

class WC_Sipay_Checkout
{

  public function __construct(){

  }

  public function setCheckoutInfo($request){
    $checkoutData = $request->get_params();
    if (is_null(WC()->session) || empty(WC()->session)) {
      WC()->session = new WC_Session_Handler();
      WC()->session->init();
    }
    WC()->session->set("sipay_session", base64_encode(json_encode($checkoutData)));

    //SHIPPING ADDRESS
    WC()->session->set("chosen_shipping_methods", $checkoutData["shipping_method"]);

    WC()->session->set( 'chosen_payment_method', "sipay_woocommerce" );

    return $request;
  }
}
