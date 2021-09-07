(function ($) {
  $(document).ready(function () {
    const main_settings_fields_ids = [
      "#woocommerce_sipay_woocommerce_ec_minicart_enabled",
      "#woocommerce_sipay_woocommerce_ec_cart_enabled",
      "#woocommerce_sipay_woocommerce_ec_product_page_enabled"
    ]
    var client = new PWall(window.sipay_ec_admin.environment, true);

    if (client.parseUrlParams("section") == "sipay_woocommerce_ec"){
      initAccordion();
    }

    function initAccordion(){
      var allPanels = $('.forminp').not(":eq(0)").hide();
      initStylesAndElements();
      $('.titledesc').not(":eq(0)").on('click', function (event) {
        event.preventDefault();
        allPanels.hide();
        resetAllArrows();
        toggleArrow($(this).parent().find('i'));
        $(this).parent().find('.forminp').show();
        initExpressCheckoutAdminSection($(this).parent().find('.forminp'));
        return false;
      });
    }

    function initExpressCheckoutAdminSection(element){
      //clean other pwalls
      $("#sipay-ec-admin").remove();
      var id          = element.find('input').attr('id');
      element.find('fieldset:nth-child(8)').after("<div id='sipay-ec-admin'>");
      $("#sipay-ec-admin").css({"margin-bottom": "2rem","border-top":"1px solid grey"})
      var backoffice = client.backoffice();
      backoffice.backendUrl(window.sipay_ec_admin.backendUrl);
      backoffice.appendTo('#sipay-ec-admin');
      backoffice.setProfile(getProfileById(id));
      backoffice.setTags("express");
      backoffice.setIsExpressCheckout(true);
      backoffice.init();
    };

    function getProfileById($id){
      if ($id == "woocommerce_sipay_woocommerce_ec_minicart_enabled"){
        return "woocommerce_minicart";
      } else if ($id == "woocommerce_sipay_woocommerce_ec_cart_enabled"){
        return "woocommerce_cart";
      } else if ($id == "woocommerce_sipay_woocommerce_ec_product_page_enabled"){
        return "woocommerce_product_page";
      }
    }

    function getExtraSettingsFields($id){
      if ($id == "#woocommerce_sipay_woocommerce_ec_minicart_enabled") {
        return [
          "#woocommerce_sipay_woocommerce_ec_minicart_container_customization",
          "#woocommerce_sipay_woocommerce_ec_minicart_container_border_color",
          "#woocommerce_sipay_woocommerce_ec_minicart_container_custom_color",
          "#woocommerce_sipay_woocommerce_ec_minicart_container_header_title",
          "#woocommerce_sipay_woocommerce_ec_minicart_container_header_font",
          "#woocommerce_sipay_woocommerce_ec_minicart_container_descriptive_text",
          "#woocommerce_sipay_woocommerce_ec_minicart_container_descriptive_font",
          "#woocommerce_sipay_woocommerce_ec_minicart_position_mode",
          "#woocommerce_sipay_woocommerce_ec_minicart_position_selector",
          "#woocommerce_sipay_woocommerce_ec_minicart_position_insertion",
          "#woocommerce_sipay_woocommerce_ec_minicart_position_style"
        ];
      } else if ($id == "#woocommerce_sipay_woocommerce_ec_cart_enabled") {
        return [
          "#woocommerce_sipay_woocommerce_ec_cart_container_customization",
          "#woocommerce_sipay_woocommerce_ec_cart_container_border_color",
          "#woocommerce_sipay_woocommerce_ec_cart_container_custom_color",
          "#woocommerce_sipay_woocommerce_ec_cart_container_header_title",
          "#woocommerce_sipay_woocommerce_ec_cart_container_header_font",
          "#woocommerce_sipay_woocommerce_ec_cart_container_descriptive_text",
          "#woocommerce_sipay_woocommerce_ec_cart_container_descriptive_font",
          "#woocommerce_sipay_woocommerce_ec_cart_position_mode",
          "#woocommerce_sipay_woocommerce_ec_cart_position_selector",
          "#woocommerce_sipay_woocommerce_ec_cart_position_insertion",
          "#woocommerce_sipay_woocommerce_ec_cart_position_style"
        ];
      } else if ($id == "#woocommerce_sipay_woocommerce_ec_product_page_enabled") {
        return [
          "#woocommerce_sipay_woocommerce_ec_product_page_container_customization",
          "#woocommerce_sipay_woocommerce_ec_product_page_container_border_color",
          "#woocommerce_sipay_woocommerce_ec_product_page_container_custom_color",
          "#woocommerce_sipay_woocommerce_ec_product_page_container_header_title",
          "#woocommerce_sipay_woocommerce_ec_product_page_container_header_font",
          "#woocommerce_sipay_woocommerce_ec_product_page_container_descriptive_text",
          "#woocommerce_sipay_woocommerce_ec_product_page_container_descriptive_font",
          "#woocommerce_sipay_woocommerce_ec_product_page_position_mode",
          "#woocommerce_sipay_woocommerce_ec_product_page_position_selector",
          "#woocommerce_sipay_woocommerce_ec_product_page_position_insertion",
          "#woocommerce_sipay_woocommerce_ec_product_page_position_style"
        ];
      }
    }

    function initStylesAndElements(){
      $('.form-table > tbody > tr > .titledesc').not(":eq(0)").append('<i class="ez-arrow down">');
      $('th.titledesc').not(':eq(0)').css({ 'padding': '10px 20px', 'background-color': '#ccc' });
      $('tr').not(':eq(0)').css({'background-color': '#ececec' });
      //$('.forminp').not(":eq(0)").css({'background-color':'#FFFFFF'});
      //$('.form-table > tbody > tr').css({"padding":"1rem","border-top":"1px solid black","border-bottom":"1px solid black"});
      $.each(main_settings_fields_ids, function(key,value){
        var $new_parent = $(value).closest('.forminp');
        var extra_settings = getExtraSettingsFields(value);
        $.each(extra_settings, function(key, value){
          var $extra_fieldset = $(value).closest('fieldset');
          var $old_parent = $(value).closest('tr');
          $new_parent.append($extra_fieldset);
          $old_parent.detach();
          var $fieldset_label = $extra_fieldset.find('span');
          $fieldset_label.css({
            "display":"block"
          });
          $extra_fieldset.css({
            "border-top": "1px solid gray",
            "padding": "0.5rem 0"
          })
          $extra_fieldset.prepend($fieldset_label);
        })
      });

      $("input[id*='container_custom_color']").on("change paste keyup", function() {
      var value = $(this).val();
      if (!/^#([0-9A-F]{3}){1,2}$/i.test(value)) {
        $(this).addClass("error");
        if (!$(this).parent().find(".custom-color-error").length) {
          $(this).parent().append('<div generated="true" class="error inline custom-color-error">' + sipay_ec_admin.custom_color_error_message + '</div>');
        }
      } else {
        $(this).removeClass("error");
        if ($(this).parent().find(".custom-color-error").length) {
          $(this).parent().find(".custom-color-error").remove();
        }
      }
    });
    }

    function toggleArrow(element){
      element.addClass('up').removeClass('down');
    }

    function resetAllArrows(){
      $('.ez-arrow').addClass('down').removeClass('up');
    }
    
  });
})(jQuery);