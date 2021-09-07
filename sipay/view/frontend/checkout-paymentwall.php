<?php
require_once __ROOT__ . '/helpers/expresscheckout-helper.php';
require_once __ROOT__ . '/helpers/constants.php';

if(!function_exists('checkout_enqueue_scripts')){
  function checkout_enqueue_scripts($environment_url, $environment){
    $express_checkout_helper = new WC_Sipay_ExpressCheckout_Helper();
    wp_enqueue_script('sipay-sdk', $environment_url.'/pwall_sdk/pwall_sdk.bundle.js', array(), '1.3', false);
    wp_enqueue_script('sipay-app-sdk', SDK_JS_URL, array(), '6.0', false);
    wp_enqueue_script('sipay-paymentwall', plugins_url('sipay/view/frontend/js/sipay-checkout-paymentwall.js'), array('jquery','sipay-sdk'), '1.3', true );
    wp_localize_script('sipay-paymentwall', 'ezenit', ["quote_rest" => get_rest_url(null, '/sipay/v1/quote'),
                                                       "checkout_rest" => get_rest_url(null, '/sipay/v1/checkout'),
                                                       "checkout_data" => get_rest_url(null, '/sipay/v1/checkout_data'),
                                                       "backend_rest" => get_rest_url(null, '/sipay/v1/actions'),
                                                       "nonce" => wp_create_nonce('wp_rest'),
                                                       "form_check_lang" => __('Check missing or invalid fields','sipay'),
                                                       "app_js" => $environment_url.'/pwall_app/js/app.js',
                                                       "environment" => $environment,
                                                       "sipay_id" => "payment_method_sipay_woocommerce",
                                                       "sipay_hash" => "sipay-app",
                                                       "quote_tags" => $express_checkout_helper->getTagsFromCart()]);
  }
}
if(!function_exists('render_checkout_paymentwall')){
  function render_checkout_paymentwall($environment){
    ?>
    <div id=sipay-app></div>
    <?php
  }
}
?>
