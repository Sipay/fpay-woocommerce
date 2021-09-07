<?php

require_once __ROOT__ . '/helpers/constants.php';

if (!function_exists("enqueue_admin_scripts")) {
  function enqueue_admin_scripts($environment)
  {
    wp_enqueue_script('sipay-sdk', $environment . '/pwall_sdk/pwall_sdk.bundle.js', array(), '1.0', true);
    wp_enqueue_script('sipay-app-sdk', SDK_JS_URL, array(), '6.0', false);
  }
}
if (!function_exists("enqueue_styles")) {
  function enqueue_styles($environment)
  {
    wp_enqueue_style('pwall-css', $environment . '/pwall_app/css/app.css');
  }
}
if (!function_exists("sipay_admin_paymentwall")) {
  function sipay_admin_paymentwall($enviroment)
  {
?>

    <script>
      // Create the div
      var div = document.createElement('div');
      div.setAttribute('id', 'sipay-app');

      // Append div to form
      document.querySelector('#woocommerce_sipay_woocommerce_0').parentElement.appendChild(div);

      document.querySelector('#sipay-app').style.background = "#FFF";

      // Remove unused field
      var elem = document.querySelector('#woocommerce_sipay_woocommerce_0');
      elem.parentNode.removeChild(elem);

      const client = new PWall('<?php echo $enviroment ?>', false);
      var backoffice = client.backoffice();
      backoffice.backendUrl("<?php echo get_rest_url(null, '/sipay/v1/actions') ?>");
      backoffice.appendTo("#sipay-app");
      backoffice.init();
    </script>
<?php }
}
?>
