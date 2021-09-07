<?php

/**
 * @package Sipay_PaymentWall
 * @version 6.0.0
 */
/*
 Plugin Name: FastPay
 Description: Fastpay acepta múltiples formas de pago (Tarjetas Visa, MasterCard, AmericanExpress, UnionPayInternational, JCB, Discover y Dinners), Amazon Pay, PayPal, Google Pay, Apple Pay, Bizum, pago por transferencia y pago financiado a través del muro de pagos de Sipay. ¡Todos los métodos de pago con una sola integración que ayudan a los comercios a vender más!
 Author: Sipay
 Author URI:        https://sipay.es/metodos-pago/
 Version: 6.0.0
 Text Domain:       woocommerce-payment-wall
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once __DIR__ . '/sdk/autoload.php';

class WC_Sipay_Payment
{

    public function init()
    {
        if (class_exists('WC_Payment_Gateway')) {
            define('__ROOT__', dirname(__FILE__));
            require_once __ROOT__ . '/model/sipay-admin-paymentwall.php';
            require_once __ROOT__ . '/model/sipay-admin-expresscheckout.php';
            require_once __ROOT__ . '/controllers/sipay-backend.php';
            require_once __ROOT__ . '/controllers/sipay-checkout.php';
            require_once __ROOT__ . '/controllers/sipay-quote.php';
            require_once __ROOT__ . '/controllers/sipay-ec-quote.php';
            include_once __ROOT__ . '/logger/sipay-payment-log.php';
            require_once __ROOT__ . '/view/frontend/express_checkout/product_page.php';
            require_once __ROOT__ . '/view/frontend/express_checkout/minicart.php';
            require_once __ROOT__ . '/view/frontend/express_checkout/cart.php';
            include_once dirname(WC_PLUGIN_FILE) . '/includes/admin/wc-admin-functions.php';
            require_once __ROOT__ . '/view/admin/admin-order-sipay-extradata.php';

            if (!session_id()) {
                session_start();
                if (!isset(WC()->session)) {
                    WC()->session = new WC_Session_Handler();
                    if (method_exists(WC()->session, "init")) { //Compatibility for 3.0-3.6
                        WC()->session->init();
                    }
                }
            }
            add_filter('woocommerce_payment_gateways', function ($methods) {
                $methods[] = 'WC_Sipay_Paymentwall';
                //$methods[] = 'WC_Sipay_ExpressCheckout';
                return $methods;
            });
            add_action(
                'rest_api_init',
                function () {
                    register_rest_route(
                        'sipay/v1',
                        '/actions',
                        array(
                            'methods' => 'POST',
                            'callback' => function ($request) {
                                $pwall = new WC_Sipay_Backend();
                                return $pwall->actions($request);
                            },
                            'args' => array(
                                'method',
                                'request_id'
                            ),
                            'permission_callback' => '__return_true'
                        )
                    );
                }
            );
            add_action(
                'rest_api_init',
                function () {
                    register_rest_route(
                        'sipay/v1',
                        '/quote',
                        array(
                            'methods' => 'POST, GET',
                            'callback' => function ($request) {
                                $pwall = new WC_Sipay_Quote();
                                return $pwall->getQuoteInfo($request);
                            },
                            'args' => array(),
                            'permission_callback' => '__return_true'
                        )
                    );
                }
            );
            add_action(
                'rest_api_init',
                function () {
                    register_rest_route(
                        'sipay/v1',
                        '/checkout',
                        array(
                            'methods' => 'POST, GET',
                            'callback' => function ($request) {
                                $pwall = new WC_Sipay_Checkout();
                                return $pwall->setCheckoutInfo($request);
                            },
                            'args' => array(
                                'checkout_data',
                            ),
                            'permission_callback' => '__return_true'
                        )
                    );
                }
            );
            add_action(
                'rest_api_init',
                function () {
                    register_rest_route(
                        'sipay/v1',
                        '/quote-ec',
                        array(
                            'methods' => 'POST, GET',
                            'callback' => function ($request) {
                                $pwall = new WC_Sipay_Ec_Quote();
                                return $pwall->getQuoteInfo($request);
                            },
                            'args' => array(
                            ),
                            'permission_callback' => '__return_true'
                        )
                    );
                }
            );
            add_action(
                'wp_head',
                function () {
                    if (WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_ec_settings[enabled]') == "yes") {
                        $environment = WC_Admin_Settings::get_option('woocommerce_sipay_woocommerce_settings[sipay_environment]');
                        $environment_url = SIPAY_ENVIROMENTS_URLS[$environment];
                        wp_enqueue_script('sipay-sdk', $environment_url . '/pwall_sdk/pwall_sdk.bundle.js');
                        wp_enqueue_script('sipay-js-sdk', SDK_JS_URL);
                        wp_enqueue_style('pwall-css', $environment_url . '/pwall_app/css/app.css');
                        wp_enqueue_script('sipay-expresscheckout-state', plugins_url('sipay/view/frontend/js/sipay-expresscheckout-state.js'), ['jquery']);
                        wp_enqueue_script('sipay-expresscheckout', plugins_url('sipay/view/frontend/js/sipay-expresscheckout-paymentwall.js'), ['sipay-expresscheckout-state']);
                    }
                }
            );
            add_action('woocommerce_admin_order_data_after_order_details', 'sipay_display_order_data_in_admin');
            add_filter('woocommerce_default_address_fields', 'sipay_disable_state_fields_validation');

            function sipay_disable_state_fields_validation($address_fields_array)
            {
                if(!is_checkout()){
                    unset($address_fields_array['billing_state']['validate']);
                    unset($address_fields_array['shipping_state']['validate']);
                    $address_fields_array['state']['required'] = false;
                }
                return $address_fields_array;
            }
        }
    }
}

add_action('init', array(new WC_Sipay_Payment(), 'init'));

register_activation_hook(__FILE__, function () {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    global $wpdb;

    $collate = '';

    if ($wpdb->has_cap('collation')) {
        $collate = $wpdb->get_charset_collate();
    }

    $table_name = $wpdb->prefix . "sipay_checkout_data";

    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
        $sql = "
        CREATE TABLE $table_name (
            cookie_hash char(32) NOT NULL,
            checkout_data longtext NOT NULL,
            PRIMARY KEY  (cookie_hash)
          ) $collate;";
        dbDelta($sql);
        $plugin_data = get_plugin_data(__FILE__);
        $plugin_version = $plugin_data['Version'];
        add_option('sipay_version', $plugin_version);
    }
    flush_rewrite_rules();
});

add_filter('generate_rewrite_rules', function ($wp_rewrite) {
    $wp_rewrite->rules = array_merge(
        ['sipay-payment/?$' => 'index.php?sipay=1'],
        $wp_rewrite->rules
    );
});

add_filter('query_vars', function ($query_vars) {
    $query_vars[] = 'sipay';
    return $query_vars;
});

add_action('template_redirect', function () {
    $custom = intval(get_query_var('sipay'));
    if ($custom) {
        include dirname(__FILE__) . '/view/frontend/sipay-payment/review.php';
        die;
    }
});
