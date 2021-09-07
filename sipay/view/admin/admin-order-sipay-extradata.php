<?php
if (!function_exists("sipay_display_order_data_in_admin")) {
  function sipay_display_order_data_in_admin($order)
  {

    $response = get_post_meta($order->get_id(), '_sipay_response_info_meta', true);
    if($response){
        wp_enqueue_style('sipay-collapsible-css', plugins_url('sipay/view/admin/css/express-checkout-admin.css'));

    ?>
    <div class="order_data_column">
      <h4><?php _e('Extra Details'); ?></h4>
      <?php
      echo '<table class="sipay_payment_info">';
      foreach($response as $key => $value){
        if(strpos($key, '.resource.') === false){
          echo '<tr>';

          echo '<th><strong>' . __($key) . '</strong></th>';
          echo '<th>' . $value . '</th>';
          echo '</tr>';
        }
      }
      echo '</table>';
    ?>      
    </div>
<?php }}
}
