/**
 * @file
 * Provides test markup for Drupal.theme.message callback.
 */

/* eslint-env es6:false, node:false */
/* eslint-disable strict, func-names, object-shorthand, no-var, prefer-template, prefer-arrow-callback */
(function($, Drupal) {
  "use strict";

  $.extend(Drupal.Message, {
    getMessageTypeLabels: function() {
      return {
        status: Drupal.t("Status message"),
        error: Drupal.t("Error message"),
        warning: Drupal.t("Warning message"),
        // Our additions.
        custom: Drupal.t("Custom message"),
        info: Drupal.t("Info message")
      };
    }
  });

  Drupal.behaviors.messageTest = {
    attach: function(context, settings) {
      $("body", context)
        .once("messageTest")
        .each(function() {
          var messenger = new Drupal.Message();

          Object.keys(settings.message || {}).forEach(function(messageType) {
            if (settings.message[messageType]) {
              messenger.add(settings.message[messageType], {
                type: messageType
              });
            }
          });
        });
    }
  };
})(jQuery, Drupal);
/* eslint-enable strict, func-names, object-shorthand, no-var, prefer-template, prefer-arrow-callback */
/* eslint-env es6:true, node:true */
