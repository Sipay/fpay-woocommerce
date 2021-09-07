<?php
if (!function_exists("sipay_ec_admin_payment_config")) {
  function sipay_ec_admin_payment_config()
  {
    $settings = array(
      'enabled' => array(
        'title'       => __('Enable/Disable', 'woocommerce-payment-wall'),
        'label'       => __('Enable Express Checkout', 'woocommerce-payment-wall'),
        'type'        => 'checkbox',
        'description' => '',
        'default'     => 'no',
      ),
      'product_page_enabled' => array(
        'title' => __('Product page', 'woocommerce-payment-wall'),
        'label' => __('Product page Enable Sipay', 'woocommerce-payment-wall'),
        'type' => 'checkbox',
        'description' => '',
        'default' => 'no',
      ),
      'product_page_container_customization' => array(
        'title' => __('Custom container', 'woocommerce-payment-wall'),
        'label' => __('Enable', 'woocommerce-payment-wall'),
        'type' => 'checkbox',
        'description' => '',
        'default' => 'no',
      ),
      'product_page_container_border_color' => array(
        'title'         => __('Container border color', 'woocommerce-payment-wall'),
        'type'          => 'select',
        'description'   => __('Select color (border and text)', 'woocommerce-payment-wall'),
        'options'       => array(
          '#FFFFFF' => 'Light',
          '#000000' => 'Dark',
          '#' => 'Custom'
        )
      ),
      'product_page_container_custom_color' => array(
        'title'         => __('Container border custom color', 'woocommerce-payment-wall'),
        'type'          => 'text',
        'placeholder'   => __('Ex. #F1F1F1', 'woocommerce-payment-wall'),
        'description'   => __(
          'Only applied if "Border color" option is "Custom"',
          'woocommerce-payment-wall'
        )
      ),
      'product_page_container_header_title' => array(
        'title'         => __('Container header title', 'woocommerce-payment-wall'),
        'type'          => 'text'
      ),
      'product_page_container_header_font' => array(
        'title'         => __('Container header font', 'woocommerce-payment-wall'),
        'description'   => __("If you want a custom font that is not included in the selector leave it in 'Without custom font' option and apply the font to #sipay_ec_container on your CSS stylesheet", 'woocommerce-payment-wall'),
        'type'          => 'select',
        'options'       => array(
          '-' => __('Without custom font', 'woocommerce-payment-wall'),
          'Arial, Arial, Helvetica' => 'Arial',
          'Arial Black, Arial Black, Gadget' => 'Arial Black',
          'Comic Sans MS' => 'Comic Sans MS',
          'Courier New' => 'Courier New',
          'Georgia' => 'Georgia',
          'Impact, Impact, Charcoal' => 'Impact',
          'Lucida Console, Monaco' => 'Lucida Console',
          'Lucida Sans Unicode, Lucida Grande' => 'Lucida Sans Unicode',
          'Palatino Linotype, Book Antiqua, Palatino' => 'Palatino',
          'Tahoma, Geneva' => 'Tahoma',
          'Trebuchet MS' => 'Trebuchet MS',
          'Verdana, Verdana, Geneva' => 'Verdana',
          'Symbol' => 'Symbol',
          'Webdings' => 'Webdings',
          'Wingdings, Zapf Dingbats',
          'MS Sans Serif, Geneva' => 'MS Sans Serif',
          'MS Serif, New York' => 'MS Serif'
        )
      ),
      'product_page_container_descriptive_text' => array(
        'title'         => __('Container descriptive text', 'woocommerce-payment-wall'),
        'type'          => 'text'
      ),
      'product_page_container_descriptive_font' => array(
        'title'         => __('Container descriptive font', 'woocommerce-payment-wall'),
        'description'   => __("If you want a custom font that is not included in the selector leave it in 'Without custom font' option and apply the font to #sipay_ec_container on your CSS stylesheet", 'woocommerce-payment-wall'),
        'type'          => 'select',
        'options'       => array(
          '-' => __('Without custom font', 'woocommerce-payment-wall'),
          'Arial, Arial, Helvetica' => 'Arial',
          'Arial Black, Arial Black, Gadget' => 'Arial Black',
          'Comic Sans MS' => 'Comic Sans MS',
          'Courier New' => 'Courier New',
          'Georgia' => 'Georgia',
          'Impact, Impact, Charcoal' => 'Impact',
          'Lucida Console, Monaco' => 'Lucida Console',
          'Lucida Sans Unicode, Lucida Grande' => 'Lucida Sans Unicode',
          'Palatino Linotype, Book Antiqua, Palatino' => 'Palatino',
          'Tahoma, Geneva' => 'Tahoma',
          'Trebuchet MS' => 'Trebuchet MS',
          'Verdana, Verdana, Geneva' => 'Verdana',
          'Symbol' => 'Symbol',
          'Webdings' => 'Webdings',
          'Wingdings, Zapf Dingbats',
          'MS Sans Serif, Geneva' => 'MS Sans Serif',
          'MS Serif, New York' => 'MS Serif'
        )
      ),
      'product_page_position_mode' => array(
        'title'         => __('Position mode', 'woocommerce-payment-wall'),
        'type'          => 'select',
        'options'       => array(
          '0' => 'Automatic',
          '1' => 'Manual'
        ),
        'default' => '0',
      ),
      'product_page_position_selector' => array(
        'title'         => __('Position DOM selector', 'woocommerce-payment-wall'),
        'placeholder'   => __('Ex. #example'),
        'description'   => __('Select the reference object in which you want to place the widget for a more custom configuration'),
        'type'          => 'text'
      ),
      'product_page_position_insertion' => array(
        'title'         => __('Position Insertion', 'woocommerce-payment-wall'),
        'type'          => 'select',
        'options'       => array(
          'before' => 'Before',
          'into' => 'Into',
          'after' => 'After'
        ),
        'description'   => __(
          'Only applied if "Position mode" option is "Manual". Select where do you wanna put the widget relative to the reference object selected in the previous field',
          'woocommerce-payment-wall'
        )
      ),
      'product_page_position_style' => array(
        'title'         => __('DOM CSS custom style', 'woocommerce-payment-wall'),
        'description'   => __('Ex. {"background-color":"red","color":"white"}', 'woocommerce-payment-wall'),
        'type'          => 'textarea'
      ),
      'minicart_enabled' => array(
        'title' => __('Minicart', 'woocommerce-payment-wall'),
        'label' => __('Minicart Enable Sipay', 'woocommerce-payment-wall'),
        'type' => 'checkbox',
        'description' => '',
        'default' => 'no',
      ),
      'minicart_container_customization' => array(
        'title' => __('Custom container', 'woocommerce-payment-wall'),
        'label' => __('Enable', 'woocommerce-payment-wall'),
        'type' => 'checkbox',
        'description' => '',
        'default' => 'no',
      ),
      'minicart_container_border_color' => array(
        'title'         => __('Container border color', 'woocommerce-payment-wall'),
        'type'          => 'select',
        'description'   => __('Select color (border and text)', 'woocommerce-payment-wall'),
        'options'       => array(
          '#FFFFFF' => 'Light',
          '#000000' => 'Dark',
          '#' => 'Custom'
        )
      ),
      'minicart_container_custom_color' => array(
        'title'         => __('Container border custom color', 'woocommerce-payment-wall'),
        'type'          => 'text',
        'placeholder'   => __('Ex. #F1F1F1', 'woocommerce-payment-wall'),
        'description'   => __(
          'Only applied if "Border color" option is "Custom"',
          'woocommerce-payment-wall'
        )
      ),
      'minicart_container_header_title' => array(
        'title'         => __('Container header title', 'woocommerce-payment-wall'),
        'type'          => 'text'
      ),
      'minicart_container_header_font' => array(
        'title'         => __('Container header font', 'woocommerce-payment-wall'),
        'description'   => __("If you want a custom font that is not included in the selector leave it in 'Without custom font' option and apply the font to #sipay_ec_container on your CSS stylesheet", 'woocommerce-payment-wall'),
        'type'          => 'select',
        'options'       => array(
          '-' => __('Without custom font', 'woocommerce-payment-wall'),
          'Arial, Arial, Helvetica' => 'Arial',
          'Arial Black, Arial Black, Gadget' => 'Arial Black',
          'Comic Sans MS' => 'Comic Sans MS',
          'Courier New' => 'Courier New',
          'Georgia' => 'Georgia',
          'Impact, Impact, Charcoal' => 'Impact',
          'Lucida Console, Monaco' => 'Lucida Console',
          'Lucida Sans Unicode, Lucida Grande' => 'Lucida Sans Unicode',
          'Palatino Linotype, Book Antiqua, Palatino' => 'Palatino',
          'Tahoma, Geneva' => 'Tahoma',
          'Trebuchet MS' => 'Trebuchet MS',
          'Verdana, Verdana, Geneva' => 'Verdana',
          'Symbol' => 'Symbol',
          'Webdings' => 'Webdings',
          'Wingdings, Zapf Dingbats',
          'MS Sans Serif, Geneva' => 'MS Sans Serif',
          'MS Serif, New York' => 'MS Serif'
        )
      ),
      'minicart_container_descriptive_text' => array(
        'title'         => __('Container descriptive text', 'woocommerce-payment-wall'),
        'type'          => 'text'
      ),
      'minicart_container_descriptive_font' => array(
        'title'         => __('Container descriptive font', 'woocommerce-payment-wall'),
        'description'   => __("If you want a custom font that is not included in the selector leave it in 'Without custom font' option and apply the font to #sipay_ec_container on your CSS stylesheet", 'woocommerce-payment-wall'),
        'type'          => 'select',
        'options'       => array(
          '-' => __('Without custom font', 'woocommerce-payment-wall'),
          'Arial, Arial, Helvetica' => 'Arial',
          'Arial Black, Arial Black, Gadget' => 'Arial Black',
          'Comic Sans MS' => 'Comic Sans MS',
          'Courier New' => 'Courier New',
          'Georgia' => 'Georgia',
          'Impact, Impact, Charcoal' => 'Impact',
          'Lucida Console, Monaco' => 'Lucida Console',
          'Lucida Sans Unicode, Lucida Grande' => 'Lucida Sans Unicode',
          'Palatino Linotype, Book Antiqua, Palatino' => 'Palatino',
          'Tahoma, Geneva' => 'Tahoma',
          'Trebuchet MS' => 'Trebuchet MS',
          'Verdana, Verdana, Geneva' => 'Verdana',
          'Symbol' => 'Symbol',
          'Webdings' => 'Webdings',
          'Wingdings, Zapf Dingbats',
          'MS Sans Serif, Geneva' => 'MS Sans Serif',
          'MS Serif, New York' => 'MS Serif'
        )
      ),
      'minicart_position_mode' => array(
        'title'         => __('Position mode', 'woocommerce-payment-wall'),
        'type'          => 'select',
        'options'       => array(
          '0' => 'Automatic',
          '1' => 'Manual'
        ),
        'default' => '0',
      ),
      'minicart_position_selector' => array(
        'title'         => __('Position DOM selector', 'woocommerce-payment-wall'),
        'placeholder'   => __('Ex. #example'),
        'description'   => __('Select the reference object in which you want to place the widget for a more custom configuration'),
        'type'          => 'text'
      ),
      'minicart_position_insertion' => array(
        'title'         => __('Position Insertion', 'woocommerce-payment-wall'),
        'type'          => 'select',
        'options'       => array(
          'before' => 'Before',
          'into' => 'Into',
          'after' => 'After'
        ),
        'description'   => __(
          'Only applied if "Position mode" option is "Manual". Select where do you wanna put the widget relative to the reference object selected in the previous field',
          'woocommerce-payment-wall'
        )
      ),
      'minicart_position_style' => array(
        'title'         => __('DOM CSS custom style', 'woocommerce-payment-wall'),
        'description'   => __('Ex. {"background-color":"red","color":"white"}', 'woocommerce-payment-wall'),
        'type'          => 'textarea'
      ),
      'cart_enabled' => array(
        'title' => __('Cart', 'woocommerce-payment-wall'),
        'label' => __('Cart Enable Sipay', 'woocommerce-payment-wall'),
        'type' => 'checkbox',
        'description' => '',
        'default' => 'no',
      ),
      'cart_container_customization' => array(
        'title' => __('Custom container', 'woocommerce-payment-wall'),
        'label' => __('Enable', 'woocommerce-payment-wall'),
        'type' => 'checkbox',
        'description' => '',
        'default' => 'no',
      ),
      'cart_container_border_color' => array(
        'title'         => __('Container border color', 'woocommerce-payment-wall'),
        'type'          => 'select',
        'description'   => __('Select color (border and text)', 'woocommerce-payment-wall'),
        'options'       => array(
          '#FFFFFF' => 'Light',
          '#000000' => 'Dark',
          '#' => 'Custom'
        )
      ),
      'cart_container_custom_color' => array(
        'title'         => __('Container border custom color', 'woocommerce-payment-wall'),
        'type'          => 'text',
        'placeholder'   => __('Ex. #F1F1F1', 'woocommerce-payment-wall'),
        'description'   => __(
          'Only applied if "Border color" option is "Custom"',
          'woocommerce-payment-wall'
        )
      ),
      'cart_container_header_title' => array(
        'title'         => __('Container header title', 'woocommerce-payment-wall'),
        'type'          => 'text'
      ),
      'cart_container_header_font' => array(
        'title'         => __('Container header font', 'woocommerce-payment-wall'),
        'description'   => __("If you want a custom font that is not included in the selector leave it in 'Without custom font' option and apply the font to #sipay_ec_container on your CSS stylesheet", 'woocommerce-payment-wall'),
        'type'          => 'select',
        'options'       => array(
          '-' => __('Without custom font', 'woocommerce-payment-wall'),
          'Arial, Arial, Helvetica' => 'Arial',
          'Arial Black, Arial Black, Gadget' => 'Arial Black',
          'Comic Sans MS' => 'Comic Sans MS',
          'Courier New' => 'Courier New',
          'Georgia' => 'Georgia',
          'Impact, Impact, Charcoal' => 'Impact',
          'Lucida Console, Monaco' => 'Lucida Console',
          'Lucida Sans Unicode, Lucida Grande' => 'Lucida Sans Unicode',
          'Palatino Linotype, Book Antiqua, Palatino' => 'Palatino',
          'Tahoma, Geneva' => 'Tahoma',
          'Trebuchet MS' => 'Trebuchet MS',
          'Verdana, Verdana, Geneva' => 'Verdana',
          'Symbol' => 'Symbol',
          'Webdings' => 'Webdings',
          'Wingdings, Zapf Dingbats',
          'MS Sans Serif, Geneva' => 'MS Sans Serif',
          'MS Serif, New York' => 'MS Serif'
        )
      ),
      'cart_container_descriptive_text' => array(
        'title'         => __('Container descriptive text', 'woocommerce-payment-wall'),
        'type'          => 'text'
      ),
      'cart_container_descriptive_font' => array(
        'title'         => __('Container descriptive font', 'woocommerce-payment-wall'),
        'description'   => __("If you want a custom font that is not included in the selector leave it in 'Without custom font' option and apply the font to #sipay_ec_container on your CSS stylesheet", 'woocommerce-payment-wall'),
        'type'          => 'select',
        'options'       => array(
          '-' => __('Without custom font', 'woocommerce-payment-wall'),
          'Arial, Arial, Helvetica' => 'Arial',
          'Arial Black, Arial Black, Gadget' => 'Arial Black',
          'Comic Sans MS' => 'Comic Sans MS',
          'Courier New' => 'Courier New',
          'Georgia' => 'Georgia',
          'Impact, Impact, Charcoal' => 'Impact',
          'Lucida Console, Monaco' => 'Lucida Console',
          'Lucida Sans Unicode, Lucida Grande' => 'Lucida Sans Unicode',
          'Palatino Linotype, Book Antiqua, Palatino' => 'Palatino',
          'Tahoma, Geneva' => 'Tahoma',
          'Trebuchet MS' => 'Trebuchet MS',
          'Verdana, Verdana, Geneva' => 'Verdana',
          'Symbol' => 'Symbol',
          'Webdings' => 'Webdings',
          'Wingdings, Zapf Dingbats',
          'MS Sans Serif, Geneva' => 'MS Sans Serif',
          'MS Serif, New York' => 'MS Serif'
        )
      ),
      'cart_position_mode' => array(
        'title'         => __('Position mode', 'woocommerce-payment-wall'),
        'type'          => 'select',
        'options'       => array(
          '0' => 'Automatic',
          '1' => 'Manual'
        ),
        'default' => '0',
      ),
      'cart_position_selector' => array(
        'title'         => __('Position DOM selector', 'woocommerce-payment-wall'),
        'placeholder'   => __('Ex. #example'),
        'description'   => __('Select the reference object in which you want to place the widget for a more custom configuration'),
        'type'          => 'text'
      ),
      'cart_position_insertion' => array(
        'title'         => __('Position Insertion', 'woocommerce-payment-wall'),
        'type'          => 'select',
        'options'       => array(
          'before' => 'Before',
          'into' => 'Into',
          'after' => 'After'
        ),
        'description'   => __(
          'Only applied if "Position mode" option is "Manual". Select where do you wanna put the widget relative to the reference object selected in the previous field',
          'woocommerce-payment-wall'
        )
      ),
      'cart_position_style' => array(
        'title'         => __('DOM CSS custom style', 'woocommerce-payment-wall'),
        'description'   => __('Ex. {"background-color":"red","color":"white"}', 'woocommerce-payment-wall'),
        'type'          => 'textarea'
      ),
    );

    return apply_filters('wc_sipay_woocommerce_settings', $settings);
  }
}