(function ($) {
  window.sipayUpdateExpressCheckout = function() {
    if (window.sipay_ec_config) {
      window.sipayExpressCheckoutState.setCurrentProfile(window.sipay_ec_config, window.sipay_ec_config.element);

      $(document.body).on('updated_cart_totals', function () {
        window.sipayExpressCheckoutState.setCurrentProfile(window.sipay_ec_config, window.sipay_ec_config.element);
      });
    }
  }
  window.setMinicartListeners = function() {
    window.sipay_timeout_leave = null;
    window.sipay_timeout_enter = null;
    window.sipay_last_profile = null;
    $(document).on('mouseenter', '.cart-contents', function () {
      if (window.last_profile == null) {
        window.last_profile = window.sipayExpressCheckoutState.getCurrentProfile();
      }
      if (window.sipay_ec_minicart_config) {
        clearTimeout(window.sipay_timeout_leave);
        clearTimeout(window.sipay_timeout_enter);
        window.sipay_timeout_enter = setTimeout(window.sipayExpressCheckoutState.setCurrentProfile(window.sipay_ec_minicart_config, window.sipay_ec_minicart_config.element), 500);
      }
      $(document).one('mouseleave', '.widget_shopping_cart', window.sipayMouseleave);
    });
    
  }

  window.sipayMouseleave = function (){
    clearTimeout(window.sipay_timeout_leave);
    clearTimeout(window.sipay_timeout_enter);
    window.sipay_timeout_leave = setTimeout(function () {
      if (window.last_profile) {
        window.sipayExpressCheckoutState.setCurrentProfile(last_profile.config, last_profile.element)
      }
      //window.sipayExpressCheckoutState.revertProfile()
    }, 500);
  }
  $(document).ready(window.sipayUpdateExpressCheckout);
  
})(jQuery)