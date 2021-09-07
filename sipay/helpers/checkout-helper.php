<?php


require_once __ROOT__.'/helpers/index.php';
require_once WC_ABSPATH . 'includes/wc-cart-functions.php';
include_once WC_ABSPATH . 'includes/wc-notice-functions.php';
require_once __ROOT__ . '/helpers/constants.php';

class WC_Sipay_Checkout_Helper extends WC_Checkout
{
  // CONST EXCLUDED_SUSPECTED_FRAUD_METHODS = ["azon", "altp_bizum", "altp_bankia_transfer", "altp_bankia"];

  public function placeOrderFromResponse($requestJSON, $response){
    // add payment method and convert quote to order
    return $this->convertQuoteToOrder($requestJSON, $response);
  }
  
  public function convertQuoteToOrder($request, $response){
    global $wpdb;

    WC_Sipay_Payment_Log::log("CONVERT TO QUOTE CUSTOMER");
    if(WC()->customer === null){
      $this->loadFromSession();
    }

    $checkoutData = WC()->session->get("sipay_session");
    if(!$checkoutData){
      $results = $wpdb->get_results( "SELECT checkout_data FROM ". $wpdb->prefix . "sipay_checkout_data WHERE cookie_hash = '" . WC()->session->get_session_cookie()[3]."'");
      if(count($results)){
        $checkoutData = $results[0]->checkout_data;
      }
    }else{
      WC()->session->__unset("sipay_session");
    }
    //$checkoutData = $_COOKIE["sipay_session"];
    WC_Sipay_Payment_Log::log("SESSION_DATA    " . base64_decode($checkoutData));
    $arrayData    = json_decode(base64_decode($checkoutData), true);

    WC()->session->set("chosen_shipping_methods",$arrayData["shipping_method"]);
    $is_cart_virtual = $this->getIsVirtualCart(WC()->cart);
    $data = array(
      'terms'                               => (int) "1",
      'createaccount'                       => (int) "0",
      'payment_method'                      => "sipay_woocommerce",
      'shipping_method'                     =>  WC()->session->get("chosen_shipping_methods"),
      'ship_to_different_address'           => $arrayData !== null && array_key_exists("ship_to_different_address", $arrayData) ? $arrayData["ship_to_different_address"] : false,
      'woocommerce_checkout_update_totals'  => false,
      'order_comments'                      => $arrayData["order_comments"],
      'shipping_first_name'                 => $is_cart_virtual ? "" : $this->getShippingFieldFromData('first_name', $arrayData),
      'shipping_last_name'                  => $is_cart_virtual ? "" : $this->getShippingFieldFromData('last_name', $arrayData),
      'shipping_company'                    => $is_cart_virtual ? "" : $this->getShippingFieldFromData('company', $arrayData),
      'shipping_address_1'                  => $is_cart_virtual ? "" : $this->getShippingFieldFromData('address_1', $arrayData),
      'shipping_address_2'                  => $is_cart_virtual ? "" : $this->getShippingFieldFromData('address_2', $arrayData),
      'shipping_city'                       => $is_cart_virtual ? "" : $this->getShippingFieldFromData('city', $arrayData),
      'shipping_state'                      => $is_cart_virtual ? "" : $this->getShippingFieldFromData('state', $arrayData),
      'shipping_postcode'                   => $is_cart_virtual ? "" : $this->getShippingFieldFromData('postcode', $arrayData),
      'shipping_country'                    => $is_cart_virtual ? "" : $this->getShippingFieldFromData('country', $arrayData),
      'billing_first_name'                  => $arrayData["billing_first_name"],
      'billing_last_name'                   => $arrayData["billing_last_name"],
      'billing_company'                     => $arrayData["billing_company"],
      'billing_address_1'                   => $arrayData["billing_address_1"],
      'billing_address_2'                   => $arrayData["billing_address_2"],
      'billing_city'                        => $arrayData["billing_city"],
      'billing_state'                       => $arrayData["billing_state"],
      'billing_postcode'                    => $arrayData["billing_postcode"],
      'billing_country'                     => $arrayData["billing_country"],
      'billing_email'                       => $arrayData["billing_email"],
      'billing_phone'                       => $arrayData["billing_phone"]
    );
    
    WC()->cart->calculate_totals();
    WC()->cart->calculate_shipping();

    $flatten_data = $this->flatten(json_decode($response->toJSON(), true));

    add_action('woocommerce_checkout_create_order', function($order, $data) use ($flatten_data){
      $order->update_meta_data('_sipay_response_info_meta', $flatten_data);
    }, 20, 2);

    $order_id = WC()->checkout->create_order($data);

    if($order_id !== null){
      if(WC()->customer->id != 0){
        update_post_meta($order_id, '_customer_user', WC()->customer->id);
      }
      $order = wc_get_order($order_id );
      wc_reduce_stock_levels($order->get_id());
      if (!$response->isCreatePendingOrder()) {
        $this->detectSuspectedFraud($response, $order);
      }
      WC()->cart->empty_cart(true);
      WC()->session->set('cart', array());
    }

    WC_Sipay_Payment_Log::log("CONVERTED QUOTE TO ORDER ". json_encode($order_id));
    return $order_id;
  }

  private function getShippingFieldFromData($field, $data){
    return $data["shipping_".$field] == "" || $data["shipping_" . $field] == null ? $data["billing_".$field] : $data["shipping_".$field];
  }

  public function detectSuspectedFraud($response, &$order){
    $responseAmount = $response->getPaidAmount();
    WC_Sipay_Payment_Log::log($response->toJSON());
    WC_Sipay_Payment_Log::log($responseAmount);
    if (!$responseAmount) {
      WC_Sipay_Payment_Log::log("SUSPECTED FROUD DETECTED");
      if ($order != null) {
        $order->update_status("on-hold", 'Suspected fraud, cannot find paid amount in respose', TRUE);
      }
    }else if($responseAmount != $order->get_total()){
      WC_Sipay_Payment_Log::log("SUSPECTED FROUD DETECTED");
      if($order != null){
        $order->update_status("on-hold", 'Suspected fraud, captured '. $responseAmount .' but order value is '. $order->get_total(), TRUE);
      }
    }else{
      if($order->needs_processing()){
        $order->update_status("processing", 'Order processed by Sipay PaymentWall', TRUE);
      }else{
        $order->update_status("completed", 'Order processed by Sipay PaymentWall', TRUE);
      }
    }
  }

  public function flatten($array, $prefix = '') {
    $result = array();
    foreach($array as $key=>$value) {
        if(is_array($value)) {
            $result = $result + $this->flatten($value, $prefix . $key . '.');
        }
        else {
            $result[$prefix . $key] = $value;
        }
    }
    return $result;
  }

  public function loadFromSession(){
    if(WC()->session === null) {
      $session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
      WC()->session = new $session_class();
      WC()->session->init();
    }

    if(WC()->cart === null) {
      WC()->cart = new WC_Cart();
    }
    //
    if(WC()->customer === null) {
      WC()->customer = new WC_Customer( WC()->session->get_customer_id(), true );
    }

    WC()->cart->get_cart();

    //WC()->checkout->update_session();
  }

  //NEW EXPRESS CHECKOUT

  public function setAddressAndCollectRates($response, $quote){
    $response_address  = $response->getAddress();
    $response_customer = $response->getCustomerData();
    if ($response_address /*&& $response_customer*/) {
      //set/get checkout data
      $this->setAddressToSession($this->prepareCheckoutData($response_address, $response_customer));
    
      //Estimate and get shipping methods
      $shipping_methods = WC()->cart->calculate_shipping();

      $chosen_shipping_methods = [];
      foreach($shipping_methods as $shipping_method){
        $chosen_shipping_method = $shipping_method->get_id();
        $chosen_shipping_methods[] = $chosen_shipping_method;
      }
      //Set chosen shipping methods
      WC()->session->set('chosen_shipping_methods', $chosen_shipping_methods);
      //Set shipping method to session
      $this->setAddressToSession($this->prepareCheckoutData($response_address, $response_customer, $chosen_shipping_methods));
      // Validate data
      $errors = new WP_Error();
      $this->validate_checkout($this->prepareCheckoutData($response_address, $response_customer), $errors);
      //Return errors, if any
      return $errors;
    }
  }

  private function prepareCheckoutData($address, $customer, $shipping_method = null){
    $checkoutData = [];
    $checkoutData["terms"] = "1";
    $checkoutData["createaccount"] = "0";
    $checkoutData["payment_method"] = "sipay_woocommerce_ec";
    $checkoutData["shipping_method"] = $shipping_method ? $shipping_method : "";
    $checkoutData["ship_to_different_address"] = false;
    $checkoutData["woocommerce_checkout_update_totals"] = true;
    $checkoutData["billing_first_name"] = $address["name"] == null || $address["name"] == "" ? $customer["name"] : $address["name"];
    $checkoutData["billing_last_name"] = "-";
    $checkoutData["billing_company"] = "";
    $checkoutData["billing_country"] = $address["country_code"];
    $checkoutData["billing_address_1"] = $address["address"][0];
    $checkoutData["billing_address_2"] = $address["address"][1] . " " . $address["address"][2];
    $checkoutData["billing_postcode"] = $address["zip"];
    $checkoutData["billing_city"] = $address["city"];
    $checkoutData["billing_state"] = $address["country_code"] == "ES" ? $this->getESStateByPostCode($address["zip"]) : "";
    $checkoutData["billing_phone"] = $address["phone"] ? $address["phone"] : "600000000";
    $checkoutData["billing_email"] = $customer["email"];
    $checkoutData["order_comments"] = "";
    $checkoutData["shipping_first_name"] = $checkoutData["billing_first_name"];
    $checkoutData["shipping_last_name"] = $checkoutData["billing_last_name"];
    $checkoutData["shipping_company"] = $checkoutData["billing_company"];
    $checkoutData["shipping_country"] = $checkoutData["billing_country"];
    $checkoutData["shipping_address_1"] = $checkoutData["billing_address_1"];
    $checkoutData["shipping_address_2"] = $checkoutData["billing_address_2"];
    $checkoutData["shipping_postcode"] = $checkoutData["billing_postcode"];
    $checkoutData["shipping_city"] = $checkoutData["billing_city"];
    $checkoutData["shipping_state"] = $checkoutData["billing_state"];
    return $checkoutData;
  }

  private function getESStateByPostCode($postcode){
    $first_two_chars = substr($postcode, 0 , 2);
    if(array_key_exists($first_two_chars, POSTCODE_REGIONID_SPAIN)){
      return POSTCODE_REGIONID_SPAIN[$first_two_chars];
    }
    return "";
  }

  private function setAddressToSession($checkoutData){

    global $wpdb;

    $wpdb->replace($wpdb->prefix . "sipay_checkout_data",[
      'cookie_hash' => WC()->session->get_session_cookie()[3], //only way i can find to obtain cookie hash from woocommmerce
      'checkout_data' => base64_encode(json_encode($checkoutData))
    ]);

  }

  public function nullToEmpty($value){
    if(!$value){
      return "";
    }
    return $value;
  }

  public function getPaypalItemsInfo($quote){

    $totals     = $quote->get_totals();
    $totals     = array(
      "total"     => $totals["total"],
      "shipping"  => $totals["shipping_total"], //$quote->getShippingAddress()->getShippingAmount(),
      "tax"       => $totals["total_tax"]       //$quote->getShippingAddress()->getTaxAmount()
    );

    $cart_items = [];
    foreach ($quote->get_cart() as $key => $item) {
      $product_info = $item["data"];
      $unit_price   = floatval($product_info->get_price());
      $unit_tax     = ((floatval($item["line_tax"])/intval($item["quantity"]))/100) * $unit_price;
      $cart_items[] = array(
        "name"       => $product_info->get_name(),
        "sku"        => $product_info->get_sku(),
        "qty"        => intval($item["quantity"]),
        "unit_price" => $unit_price,
        "unit_tax"   => $unit_tax,
        "is_digital" => $product_info->is_virtual()
      );
    }
    $res = \PWall\Request::buildPaypalCartInfo(get_woocommerce_currency(),$cart_items,$totals);
    return $res;
    
  }

  public function getIsVirtualCart($quote){
    $all_digital_products = true;
    foreach ($quote->get_cart() as $key => $item) {
      $product_info = $item["data"];
      $item_type_virtual = $product_info->is_virtual() || $product_info->is_downloadable();
      if (!$item_type_virtual) {
        $all_digital_products = false;
      }
    }
    return $all_digital_products;
  }

  public function setPDS2Params(&$request, $quote, $customer){
    $sipay_settings = get_option('woocommerce_sipay_woocommerce_settings');

    $tra_enabled  = $sipay_settings["sipay_psd2_tra"] === "yes" ? true : false;
    $tra_value    = floatval($sipay_settings["sipay_psd2_tra_value"]);
    $lwv_enabled  = $sipay_settings["sipay_psd2_lwv"] === "yes" ? true : false;
    $lwv_value    = floatval($sipay_settings["sipay_psd2_lwv_value"]);

    $cart_total = floatval(wc_format_decimal($quote->total, wc_get_price_decimals()));

    $checkoutData = WC()->session->get("sipay_session");
    WC_Sipay_Payment_Log::log("SESSION_DATA    " . base64_decode($checkoutData));
    $orderData    = json_decode(base64_decode($checkoutData), true);

    $is_guest = WC()->customer->get_id() == 0;
    $is_cart_virtual = $this->getIsVirtualCart(WC()->cart);

    if(!$is_guest){
      $user_data = get_userdata(WC()->customer->get_id());   
    }

    $customer_data = [];
    $customer_data["account_modification_date"] = $is_guest ? "" : $customer->get_date_modified()->date;
    $customer_data["account_creation_date"]     = $is_guest ? "" : $user_data->user_registered;
    $customer_data["account_purchase_number"]   = strval(wc_get_customer_order_count(WC()->customer->get_id()));
    $customer_data["billing_city"]              = $orderData["billing_city"];
    $customer_data["billing_country"]           = $orderData["billing_country"];
    $customer_data["billing_address_1"]         = $orderData["billing_address_1"];
    $customer_data["billing_address_2"]         = $orderData["billing_address_2"];
    $customer_data["billing_postcode"]          = $orderData["billing_postcode"];
    if ($is_cart_virtual) {
      $customer_data["delivery_email_address"]  = $orderData["billing_email"];
    }
    $customer_data["shipping_city"]             = $this->getShippingFieldFromData('city', $orderData);
    $customer_data["shipping_country"]          = $this->getShippingFieldFromData('country', $orderData);
    $customer_data["shipping_address_1"]        = $this->getShippingFieldFromData('address_1', $orderData);
    $customer_data["shipping_address_2"]        = $this->getShippingFieldFromData('address_2', $orderData);
    $customer_data["shipping_postcode"]         = $this->getShippingFieldFromData('postcode', $orderData);

    $request->setPSD2Info($tra_enabled, $tra_value, $lwv_enabled, $lwv_value, $cart_total, $customer_data);
  }

}
