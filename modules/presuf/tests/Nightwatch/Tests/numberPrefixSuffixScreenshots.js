/**
 * @file
 * Captures number widget with prefix and / or suffix, with different states.
 *
 * Error state's fileName vary by the state of the inline_form_errors module.
 */

module.exports = {
  "@tags": ["claro"],
  before(browser) {
    if (browser.drupalInstall) {
      browser.drupalInstall({
        installProfile: "clarodist"
      });
    }
  },
  after(browser) {
    if (browser.drupalUninstall) {
      browser.drupalUninstall().end();
    } else {
      browser.end();
    }
  },
  "Number prefix suffix": function test(browser) {
    ["", "he"].forEach(langprefix => {
      browser
        .resizeWindow(1024, 600)
        .smartURL(
          langprefix
            ? `/${langprefix}/contact/presuf_number`
            : "/contact/presuf_number"
        )
        .waitTillElementPresent(
          '[data-drupal-selector="edit-presuf-number-m"] td:first-child .tabledrag-handle',
          5000
        )
        .smartClick(
          '[name="presuf_number_m_add_more"]:not([disabled])',
          "mousedown"
        )
        .waitTillElementPresent('[name="presuf_number_m[1][value]"]')
        .smartClick(
          '[name="presuf_number_pre_m_add_more"]:not([disabled])',
          "mousedown"
        )
        .waitTillElementPresent('[name="presuf_number_pre_m[1][value]"]')
        .smartClick(
          '[name="presuf_number_suf_m_add_more"]:not([disabled])',
          "mousedown"
        )
        .waitTillElementPresent('[name="presuf_number_suf_m[1][value]"]')
        .smartClick(
          '[name="presuf_number_pre_suf_m_add_more"]:not([disabled])',
          "mousedown"
        )
        .waitTillElementPresent('[name="presuf_number_pre_suf_m[1][value]"]')
        .setValueAndChange('[name="presuf_number[0][value]"]', "33")
        .setValueAndChange('[name="presuf_number_m[0][value]"]', "33")
        .setValueAndChange('[name="presuf_number_pre[0][value]"]', "33")
        .setValueAndChange('[name="presuf_number_pre_m[0][value]"]', "33")
        .setValueAndChange('[name="presuf_number_suf[0][value]"]', "33")
        .setValueAndChange('[name="presuf_number_suf_m[0][value]"]', "33")
        .setValueAndChange('[name="presuf_number_pre_suf[0][value]"]', "33")
        .setValueAndChange('[name="presuf_number_pre_suf_m[0][value]"]', "33")
        .savefullScreenShot("01", langprefix)
        .smartClick("#edit-submit")
        .waitTillElementPresent("[data-drupal-messages] div", 5000)
        .elements(
          "css selector",
          ".form-item__error-message, .form-item--error-message, .fieldset__error-message",
          inlineFormMessagesQueryResults => {
            browser.savefullScreenShot(
              inlineFormMessagesQueryResults.value.length ? "03" : "02",
              langprefix,
              inlineFormMessagesQueryResults.value.length
                ? "Number prefix suffix with inline errors"
                : "Number prefix suffix with simple errors"
            );
          }
        );
    });
  }
};
