<?php

class WC_Sipay_Backend
{

protected $key;
protected $resource;
protected $secret;
protected $environment;
protected $proxy_helper;

public function __construct(){
  $this->sipay_settings = get_option('woocommerce_sipay_woocommerce_settings');
  $this->environment    = $this->sipay_settings["sipay_environment"];
  $this->key            = $this->sipay_settings["sipay_key"];
  $this->resource       = $this->sipay_settings["sipay_resource"];
  $this->secret         = $this->sipay_settings["sipay_secret"];
  $this->debug_path     = $this->sipay_settings["debug_path"];
  
  $this->checkout_helper = new WC_Sipay_Checkout_Helper();

}

public function addOrderInfo(&$pwall_request){
  if(WC()->cart === null){
    $this->checkout_helper->loadFromSession();
  }
  $quote = WC()->cart;

  $pwall_request->setOrderId($quote == null ? "000000" : strval((int)(microtime(true)*100)));
  $pwall_request->setAmount($quote == null ? "0" : floatval(wc_format_decimal($quote->total, wc_get_price_decimals())));
  if($quote->total === 0 && WC()->session->get("sipay_pending_payment")){
      $orderId = WC()->session->get("sipay_order_id");
      $order = wc_get_order($orderId);
      $pwall_request->setAmount(floatval(wc_format_decimal($order->get_total(), wc_get_price_decimals())));
  }else{
      WC()->session->__unset("sipay_pending_payment");
      WC()->session->__unset("sipay_order_id");
      $pwall_request->setAmount($quote == null ? "0" : floatval(wc_format_decimal($quote->total, wc_get_price_decimals())));
  }
    
  $pwall_request->setCurrency(get_woocommerce_currency());
  $pwall_request->setGroupId(strval(WC()->customer->get_id()));
  $pwall_request->setOriginalUrl(get_bloginfo('url'));
}

/**
 * Process payment
 *
 * @param array $request Request object
 *
 * @return array
 */
public function actions($request){
    $this->client           = new \PWall\Client();
    $jsonRequest  = $request->get_json_params();
    WC_Sipay_Payment_Log::log("ON BACKEND EXECUTE: " . json_encode($jsonRequest));

    $this->client->setEnvironment($this->environment);
    $this->client->setKey($this->key);
    $this->client->setResource($this->resource);
    $this->client->setSecret($this->secret);
    $this->client->setBackendUrl(get_site_url(null, 'sipay-payment'));
    if ($this->debug_path && $this->debug_path != '') {
      $this->client->setDebugFile($this->debug_path);
    }

    if (WC()->cart === null) {
      $this->checkout_helper->loadFromSession();
    }
    $quote = WC()->cart;
    if($quote->get_cart_contents_count() == 0){
      $request = new \PWall\Request(json_encode($jsonRequest), true);
    }else{
      $request = new \PWall\Request(json_encode($jsonRequest), false);
    }

    $this->addOrderInfo($request);

    if($request->isEcCreateOrder()||$request->hasUpdateAmount()){
      //add product info
      //       is_digital: true <- only if all element are digital
      $cart_info = $this->checkout_helper->getPaypalItemsInfo($quote);
      $request->setEcCartInfo($cart_info["items"], $cart_info["is_digital"], $cart_info["breakdown"]);
      $request->setAmount($cart_info["total"]);
    }

    //PSD2
    $this->checkout_helper->setPDS2Params($request, $quote, WC()->customer);

    $response = $this->client->proxy($request);

    if ($response->hasAddress() && !$response->hasUpdateAmount()) {
      //Set address to quote, set shipping method, collect rates
      try {
        $quote = WC()->cart;
        $error = $this->checkout_helper->setAddressAndCollectRates($response, $quote);
        if($error && $error->has_errors()){
          $response->setError(json_encode($error->get_error_messages()));
        }else{
          $response->setUpdatedAmount(floatval($quote->total));
        }
      } catch (\Exception $e) {
        $response->setError($e->getMessage());
      }
    }

    if ($response->isCreatePendingOrder() && !WC()->session->get("sipay_pending_payment")) {
      $orderId = $this->checkout_helper->placeOrderFromResponse($jsonRequest, $response);
      WC()->session->set("sipay_order_id", $orderId);
      WC()->session->set("sipay_pending_payment", true);
    } else if ($response->canPlaceOrder()) {
      if(WC()->session->get("sipay_pending_payment")){
        $orderId = WC()->session->get("sipay_order_id");
        $order = wc_get_order($orderId);
        $flatten_data = $this->checkout_helper->flatten(json_decode($response->toJSON(), true));
        $order->update_meta_data('_sipay_response_info_meta', $flatten_data);
        $order->save();
        if ($order->get_status() !== "pending") {
              $order->update_status("on-hold", __("Order was canceled by Woocommerce before we completed the transaction, please review the transaction information and check with the customer."), true);
              wc_add_notice(sprintf(__("The order %s was not in pending payment status. Please, contact with the store with the order number to resolve the issue.", "woocommerce-payment-wall"), $order->get_id()), 'error');
        }else{
          $this->checkout_helper->detectSuspectedFraud($response, $order);
        }
        $payment_gateway = new WC_Sipay_Paymentwall();
        WC()->session->__unset("sipay_order_id");
        WC()->session->__unset("sipay_pending_payment");
        setcookie("success_redirect", $payment_gateway->get_return_url(wc_get_order($orderId)), time() + 10, "/");
      }else{
        $orderId = $this->checkout_helper->placeOrderFromResponse($jsonRequest, $response);
        $payment_gateway = new WC_Sipay_Paymentwall();
        WC_Sipay_Payment_Log::log("ACTION: setCookieToRedirect");
        setcookie("success_redirect", $payment_gateway->get_return_url(wc_get_order($orderId)), time() + 10, "/");
      }
    }

    return json_decode($response->toJSON());
  }
}
