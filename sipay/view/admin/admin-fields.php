<?php
if( !function_exists("sipay_admin_payment_config") )
{
  function sipay_admin_payment_config(){
    $settings = array(
        'enabled' => array(
            'title'       => __('Enable/Disable', 'woocommerce-payment-wall'),
            'label'       => __(
                'Enable Fastpay', 'woocommerce-payment-wall'
            ),
            'type'        => 'checkbox',
            'description' => '',
            'default'     => 'yes',
        ),
        'method_title' => array(
          'title'         => __('Payment method checkout title', 'woocommerce-payment-wall'),
          'type'          => 'text',
          'description'   => __(
              'Fastpay payment method title used in checkout',
              'woocommerce-payment-wall'
          ),
          'default'       => 'Pagar con tarjeta y otros métodos de pago'
        ),
            'sipay_review_page_title' => array(
            'title'         => __('Fastpay review page title', 'woocommerce-payment-wall'),
            'type'          => 'text',
            'description'   => __(
                'This setting affects the title of the page that will be presented to customer after they been redirect back from payment gateway',
                'woocommerce-payment-wall'
            ),
            'default'       => __('Fastpay payment review')
        ),
        'sipay_key' => array(
            'title'         => __('Commerce Key', 'woocommerce-payment-wall'),
            'type'          => 'password',
            'description'   => __(
                'Sipay provided commerce key', 'woocommerce-payment-wall'
            ),
            'default'       => 'commerce-key'
        ),
        'sipay_environment' => array(
            'title'         => __('Environment', 'woocommerce-payment-wall'),
            'type'          => 'select',
            'options'       => array(
                'sandbox' => 'sandbox',
                'live' => 'live',
                'develop' => 'develop'
            ),
            'description'   => __(
                'Environment. Sandbox is for integration '.
                'tests, live for real transactions.',
                'woocommerce-payment-wall'
            )
        ),
        'sipay_secret' => array(
            'title'         => __('Sipay secret', 'woocommerce-payment-wall'),
            'type'          => 'password',
            'description'   => __(
                'Sipay provided secret', 'woocommerce-payment-wall'
            ),
            'default'       => 'secret'
        ),
        'sipay_resource' => array(
            'title'         => __('Sipay resource', 'woocommerce-payment-wall'),
            'type'          => 'text',
            'description'   => __(
                'Sipay provided resource for payment wall all',
                'woocommerce-payment-wall'
            ),
            'default'       => 'resource'
        ),
        'sipay_psd2_tra' => array(
            'title'       => __('PSD2 TRA', 'woocommerce-payment-wall'),
            'label'       => __(
                'Enable/Disable',
                'woocommerce-payment-wall'
            ),
            'type'        => 'checkbox',
            'description' => '',
            'default'     => 'no',
        ),
        'sipay_psd2_tra_value' => array(
            'title'         => __('High amount up to', 'woocommerce-payment-wall'),
            'type'          => 'text',
            'description'   => __(
                'Only applies if PSD2 TRA is enabled', 'woocommerce-payment-wall'
            ),
            'default'       => ''
        ),
        'sipay_psd2_lwv' => array(
            'title'       => __('PSD2 LWV', 'woocommerce-payment-wall'),
            'label'       => __(
                'Enable/Disable',
                'woocommerce-payment-wall'
            ),
            'type'        => 'checkbox',
            'description' => '',
            'default'     => 'no',
        ),
        'sipay_psd2_lwv_value' => array(
            'title'         => __('Low amount up to', 'woocommerce-payment-wall'),
            'type'          => 'text',
            'description'   => __(
                'Only applies if PSD2 LWV is enabled', 'woocommerce-payment-wall'
            ),
            'default'       => ''
        ),
        'debug_path' => array(
          'title'         => __('Debug path'),
          'type'          => 'text',
          'description'   => __(
              'Path where Sipay logs will be written to',
              'woocommerce-payment-wall'
          ),
        ),
        array(
            'name'    => __('extra', 'woocommerce-payment-wall'),
            'desc'    => __('Extra info, leave it empty.', 'woocommerce'),
            'id'      => "app",
            'type'    => 'pwalsettings'
        )
    );

    return apply_filters('wc_sipay_woocommerce_settings', $settings);
  }
}
