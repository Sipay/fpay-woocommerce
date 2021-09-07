(function ($) {
    var sipayExpressCheckoutState = function () {
      this.profiles           = {};
      this.currentProfile     = null;
      this.lastProfile        = null;
      this.config             = null;
      this.element            = null;
      this.elementParent      = null;
      this.hasLoadedStyle     = false;

      $.fn.serializeArrayAll = function () {
      var rCRLF = /\r?\n/g;
      return this.map(function () {
        return this.elements ? jQuery.makeArray(this.elements) : this;
      }).map(function (i, elem) {
        var val = jQuery(this).val();
        if (val == null) {
          return val == null
          //next 2 lines of code look if it is a checkbox and set the value to blank 
          //if it is unchecked
        } else if (this.type == "checkbox" && this.checked == false) {
          return { name: this.name, value: this.checked ? this.value : '' }
          //next lines are kept from default jQuery implementation and 
          //default to all checkboxes = on
        } else {
          return jQuery.isArray(val) ?
            jQuery.map(val, function (val, i) {
              return { name: elem.name, value: val.replace(rCRLF, "\r\n") };
            }) :
            { name: elem.name, value: val.replace(rCRLF, "\r\n") };
        }
      }).get();
    };

      this.addProfile = function(config, element){
        this.profiles[config.profile] = {config:config, element: element, elementParent: $(element).parent()};
      }

      this.setCurrentProfile = function(config, element){
        this.addProfile(config, element);
        this.lastProfile = this.currentProfile;
        this.removeFieldset(this.element);
        this.currentProfile = config.profile;
        this.config = config;
        this.element = element;
        this.elementParent = this.profiles[config.profile].elementParent;
        this.initExpressCheckout();
      }

      this.getCurrentProfile = function(){
        if(!this.config){
          return null;
        }
        return { config: this.config, element:this.element};
      }

      this.revertProfile = function(){
        if(!this.lastProfile){
          this.currentProfile = null;
          this.config = null;
          this.element = null;
          return;
        }
        this.removeFieldset(this.element);
        this.currentProfile = this.lastProfile;
        this.lastProfile = null;
        this.config = this.profiles[this.currentProfile].config;
        this.element = this.profiles[this.currentProfile].element;
        this.elementParent = this.profiles[this.currentProfile].elementParent;
        this.initExpressCheckout();
      }

      this._init = function(config, element, quote) {
        const client = new PWall(config.enviroment, false);
        var express_checkout = client.expresscheckout();

        express_checkout.backendUrl(config.backendUrl)
        express_checkout.appendTo(element)
        express_checkout.setProfile(config.profile)
        express_checkout.setTags(quote.tags)
        if (config.storeLogoUrl){
          express_checkout.setLogoUrl(config.storeLogoUrl)
        }
        express_checkout.on("paymentOk", this.redirectToCheckoutSuccess.bind(this))
        express_checkout.on("paymentKo", () => { console.log("PAYMENT KO") })
        express_checkout.on("validationFunc", this.validateFunction.bind(this))
        express_checkout.currency(quote.currency)
        express_checkout.groupId(quote.group_id)
        express_checkout.amount(quote.amount)
      };

      this.redirectToCheckoutSuccess = function() {
        var url_encoded = this.getCookie("success_redirect");
        this.deleteCookie("success_redirect");
        window.location.replace(decodeURIComponent(url_encoded));
      };
      /**
         * Get the value of a cookie
         * Source: https://gist.github.com/wpsmith/6cf23551dd140fb72ae7
         * @param  {String} name  The name of the cookie
         * @return {String}       The cookie value
         */
      this.getCookie = function(name) {
        var value = "; " + document.cookie;
        var parts = value.split("; " + name + "=");
        if (parts.length == 2) return parts.pop().split(";").shift();
      };
      this.deleteCookie = function(name) {
        document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
      };

      this.initExpressCheckout = function() {
        //if first #app, save configuration for later
        this.containerPosition();
        $.ajax({
          url: this.config.quoteInfoUrl,
          dataType: "json",
          data: {},
          timeout: 30000,
          success: function (data) {
            this._init(this.config, this.element, data);
          }.bind(this)
        });
      };

      this.containerPosition = function(){
        var app_element = this.element;
        var insert = "";
        $('#app').remove();
        if (Object.keys(this.config.positionConfig).length > 0) {
          //Prepare container and insert in position selected
          var app_container = "<div id=sipay_ec_app></div>";
          this.insertInPosition(this.config.positionConfig, this.element, app_container);
          app_element = "#sipay_ec_app";
          if (this.config.positionStyleConfig) {
            $(app_element).css(this.config.positionStyleConfig);
          }
          this.element = app_element;
          insert = this.config.positionConfig.insertion;
        }
        //CREATE CONTAINER WITH LEGEND AND ALL THAT HAPPY STUFF
        if (Object.keys(this.config.containerStyle).length > 0) {
          $('#sipay_ec_app').remove();
          if (this.config.containerStyle.header_title != null) {
            var fieldset = "<fieldset id=sipay_ec_container><legend style='padding: 0px 16px;' align=center>" + this.config.containerStyle.header_title + "</legend ></fieldset>";
            
          } else {
            var fieldset = "<fieldset id=sipay_ec_container><fieldset>";
          }
          if (this.config.positionStyleConfig) {
            $("#sipay_ec_container").css(this.config.positionStyleConfig);
          }
          if (insert === "into"){
            this.insertInPosition(this.config.positionConfig, $(this.element).parent(), fieldset);
          }else{
            this.insertInPosition(this.config.positionConfig, this.element, fieldset);
          }
          if (this.config.containerStyle.descriptive_text != null && this.config.containerStyle.descriptive_text != ""){
            $("fieldset#sipay_ec_container > legend").bind({
              mousemove: this.changeTooltipPosition,
              mouseenter: this.showTooltip.bind(this),
              mouseleave: this.hideTooltip
            });
          }
          var elementj = $(app_element).detach();

          if (this.config.containerStyle.color != "#") {
            $('#sipay_ec_container').css('border', '1px solid ' + this.config.containerStyle.color);
          } else {
            $('#sipay_ec_container').css('border', '1px solid ' + this.config.containerStyle.custom_color);
          }
          if (this.config.containerStyle.header_title_typo != null) {
            $('#sipay_ec_container').css('font-family', this.config.containerStyle.header_title_typo);
          }
          $('#sipay_ec_container').append(elementj);
        }
      }

      this.removeFieldset = function(app_element){
        var $fieldset = $('#sipay_ec_container');
        if ($fieldset.length && !this.config.positionConfig.length){
          var $app_element = $(app_element).detach();
          this.elementParent.append($app_element);
        }
        $fieldset.remove();
      }

      this.validateFunction = function() {
        $(document).off('mouseleave', '.widget_shopping_cart', window.sipayMouseleave);
        $('body').on('DOMNodeRemoved', '#pwall-amazonPayOverlay', function () {
          window.sipayMouseleave();
        });
        $('body').on('DOMNodeRemoved', '.paypal-checkout-sandbox', function () {
          window.sipayMouseleave();
        });
        var $form = $('form.cart');
        if ($form.length > 0 && this.currentProfile == "woocommerce_product_page") {
          var addToCartButton = $('.single_add_to_cart_button');
          if (addToCartButton.is('.disabled')) {
            if (addToCartButton.is('.wc-variation-is-unavailable')) {
              window.alert(wc_add_to_cart_variation_params.i18n_unavailable_text);
            } else if (addToCartButton.is('.wc-variation-selection-needed')) {
              window.alert(wc_add_to_cart_variation_params.i18n_make_a_selection_text);
            }
            return false;
          }
          this.ajaxSyncSubmit();
          return true;
        }
        return true;
      };

      this.ajaxSyncSubmit = function() {
        var $form = $('form.cart');
        var data = $form.find('input:not([name="product_id"]), select, button, textarea').serializeArrayAll();
        var $thisbutton = $('.single_add_to_cart_button:not(.disabled)');
        $.each(data, function (i, item) {
          if (item.name == 'add-to-cart') {
            item.name = 'product_id';
            item.value = $form.find('input[name=variation_id]').val() || $thisbutton.val();
          }
        });
        $(document.body).trigger('adding_to_cart', [$thisbutton, data]);
        $.ajax({
          type: 'POST',
          url: woocommerce_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'),
          data: data,
          async: false,
          success: function (response) {

            if (response.error & response.product_url) {
              window.location = response.product_url;
              return;
            }

            $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);
          },
        });
      };

      this.insertInPosition = function(configPosition, element, app_container) {
        if (configPosition.insertion === "into") {
          $(element).append(app_container);
          //$(app_container).appendTo(element);
        } else if (configPosition.insertion === "before") {
          $(app_container).insertBefore(element);
        } else {
          $(app_container).insertAfter(element);
        }
      };

      this.showTooltip = function (event) {
        $('div.tooltip').remove();
        $('<div class="tooltip">'+this.config.containerStyle.descriptive_text+'</div>')
          .appendTo('body').css({
            "margin": "8px",
            "padding": "8px",
            "border": "1px solid grey",
            "position": "absolute",
            "z-index": "100"
          });
        this.changeTooltipPosition(event);
      };

      this.changeTooltipPosition = function (event) {
        var tooltipX = event.pageX - 8;
        var tooltipY = event.pageY + 8;
        $('div.tooltip').css({ top: tooltipY, left: tooltipX });
      };

      this.hideTooltip = function () {
        $('div.tooltip').remove();
      };
    }
    window.sipayExpressCheckoutState = new sipayExpressCheckoutState();
  })(jQuery);