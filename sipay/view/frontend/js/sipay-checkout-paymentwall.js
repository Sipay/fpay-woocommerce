(function ($) {
  $(document).ready(function () {
    const client = new PWall(window.ezenit.environment, true);
    var checkout = client.checkout();
    $('body').on('updated_checkout', function () {
      initialize();
    });

    function initialize() {
      renderPaymentWall();
      addListeners();
    }

    function renderPaymentWall() {
      log("RENDERING PAYMENT WALL");
      if (!$('#pwallappjs').length) {
        checkout.appendTo("#sipay-app")
          .backendUrl(window.ezenit.backend_rest)
          .validateForm(function () { return true; })
          .submitButton('#place_order')
          .setTags(window.ezenit.quote_tags)
          .on("validationFunc", checkCheckoutFields.bind(this))
          .on("beforeValidation", updateCustomerData.bind(this))
          .on("paymentOk", redirectToCheckoutSuccess.bind(this));
        updatePaymentWallDataset(checkout, true);
      } else {
        updatePaymentWallDataset(checkout);
      }
    }

    function addListeners() {
      $('#payment').on('change', function () {
        checkout.isSelected($('#' + window.ezenit.sipay_id).is(':checked'));
      })
    }

    function updateCustomerData() {
      var serializeForm = $('form.checkout :not(#_wpnonce)').serialize();
      $.ajax({
        url: window.ezenit.checkout_rest,
        type: 'POST',
        data: serializeForm,
        async: false
      }).done(function (data) {
        console.log("OK");
      }).fail(function (data) {
        console.log("FAIL");
      });
    }

    // function updateCheckoutField() {
    //   $.ajax({
    //     url: window.ezenit.checkout_data,
    //     type: 'GET',
    //     async: false,
    //   }).done(function (data) {
    //     console.log("OK");
    //     try{
    //       var array_data = JSON.parse(data);
    //     }catch(e){

    //     }
    //     $.each(array_data, function(key, value){
    //       var input = $('[name='+key+']');
    //       if(input.val() === ''){
    //         input.val(value);
    //         input.trigger("change");
    //       }
    //     });
    //   }).fail(function (data) {
    //     console.log("FAIL");
    //   });
    // }

    function updatePaymentWallDataset(checkout, isSet = false) {
      $.ajax({
        url: window.ezenit.quote_rest,
        async: false
      }).done(function (data) {
        if (isSet) {
          checkout.groupId(data.groupId)
            .currency(data.currency)
            .isSelected($('#' + window.ezenit.sipay_id).is(':checked'))
            .amount(data.amount)
        } else {
          checkout.amount(data.amount)
        }
      }).fail(function (data) {
        log("FAILED TO RETRIEVE QUOTE INFO")
      });
    }

    function checkCheckoutFields() {
      var valid = true;
      updateCustomerData();
      $('.woocommerce-NoticeGroup-checkout, .woocommerce-error, .woocommerce-message').remove();

      $(".validate-required").each(function () {
        var validate_elements = $(this).find('.input-text, select, input:checkbox');
        if (validate_elements.length != 0) {
          $(this).find('.input-text, select, input:checkbox').trigger("validate")
          valid = valid && ($(this).hasClass("woocommerce-validated") || $(this).is(":hidden"));
        }
      });
      console.log("FIELDS VALIDATION ----> " + valid);
      if (!valid) {
        addAndScrolltoErrorMessage();
      }
      return valid;
    }

    function addAndScrolltoErrorMessage() {
      var $checkout_form = $('form.checkout')
      $checkout_form.prepend('<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout"><div class="woocommerce-error">' + window.ezenit.form_check_lang + '</div></div>');
      $checkout_form.removeClass('processing').unblock();
      $checkout_form.find('.input-text, select, input:checkbox').trigger('validate').blur();
      $(document.body).trigger('checkout_error');
      //Scroll to notice
      var scrollElement = $('.woocommerce-NoticeGroup-updateOrderReview, .woocommerce-NoticeGroup-checkout');

      if (!scrollElement.length) {
        scrollElement = $('.form.checkout');
      }
      $.scroll_to_notices(scrollElement);
    }

    function redirectToCheckoutSuccess() {
      var url_encoded = getCookie("success_redirect");
      deleteCookie("success_redirect");
      window.location.replace(decodeURIComponent(url_encoded));
    }

    /**
     * Get the value of a cookie
     * Source: https://gist.github.com/wpsmith/6cf23551dd140fb72ae7
     * @param  {String} name  The name of the cookie
     * @return {String}       The cookie value
     */
    function getCookie(name) {
      var value = "; " + document.cookie;
      var parts = value.split("; " + name + "=");
      if (parts.length == 2) return parts.pop().split(";").shift();
    };

    function deleteCookie(name) {
      document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    };

    function log() {
      var args = Array.prototype.slice.call(arguments, 0);
      args.unshift("[SIPAY DEBUG]");
      console.log.apply(console, args);
    }
  })
})(jQuery);
