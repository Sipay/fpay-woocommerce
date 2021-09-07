(function ($) {
  $(document).ready(function () {
    const client = new PWall(window.sipay_checkout_review.enviroment, false);
    var checkout = client.checkout();

    checkout.backendUrl(window.sipay_checkout_review.backendUrl)
      .appendTo("#sipay_app_review")
      .on("paymentOk", redirectToCheckoutSuccess)
      .on("validationFunc", function () {
        return true;
      })
      .setTags("fisico")
      .isSelected(true)
      .validateForm(function () {
        return true;
      })
      .currency(window.sipay_checkout_review.currency)
      .groupId(window.sipay_checkout_review.customer_id)
      .amount(window.sipay_checkout_review.amount);

    function redirectToCheckoutSuccess() {
      var url_encoded = getCookie("success_redirect");
      deleteCookie("success_redirect");
      window.location.replace(decodeURIComponent(url_encoded));
    };

    function getCookie(name) {
      var value = "; " + document.cookie;
      var parts = value.split("; " + name + "=");
      if (parts.length == 2) return parts.pop().split(";").shift();
    };
    function deleteCookie(name) {
      document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    };
  });
})(jQuery);