<?php
if (WC()->session->get("sipay_pending_payment")) {
      $orderId = WC()->session->get("sipay_order_id");
      $order = wc_get_order($orderId);
} else {
      wp_redirect(home_url());
      exit();
}
require_once __ROOT__ . '/helpers/constants.php';
require_once __ROOT__ . '/helpers/expresscheckout-helper.php';

$express_checkout_helper = new WC_Sipay_ExpressCheckout_Helper();

$environment = WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_settings[sipay_environment]');
$environment_url = SIPAY_ENVIROMENTS_URLS[$environment];

wp_enqueue_script('sipay-js-sdk', SDK_JS_URL);
wp_enqueue_script('sipay-sdk', $environment_url . '/pwall_sdk/pwall_sdk.bundle.js');
wp_enqueue_style('pwall-css', $environment_url . '/pwall_app/css/app.css');
wp_enqueue_style('pwall-review-css', plugins_url('sipay/view/frontend/css/sipay-review.css'));

get_header();

wp_enqueue_script('sipay-checkout-review', plugins_url('sipay/view/frontend/js/sipay-checkout-review.js'), ['jquery']);
wp_localize_script('sipay-checkout-review', 'sipay_checkout_review', [
      "enviroment"      => WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_settings[sipay_environment]'),
      "backendUrl"      => get_rest_url(null, '/sipay/v1/actions'),
      "currency"        => get_woocommerce_currency(),
      "amount"          => floatval($order->get_total()),
      "customer_id"     => WC()->customer ? strval(WC()->customer->get_id()) : "0",
      "tags"            => $express_checkout_helper->getTagsFromOrder($order),
]);
?>
      <h1><?php echo WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_settings[sipay_review_page_title]') ?></h1>
      <div id="sipay_app_review"></div>
<?php
get_footer();
