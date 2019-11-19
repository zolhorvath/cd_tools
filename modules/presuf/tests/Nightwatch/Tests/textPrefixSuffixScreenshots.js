/**
 * @file
 * Captures text widget with prefix and / or suffix, with different states.
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
  "Text prefix suffix": function test(browser) {
    const text = "Short text with error";
    ["", "he"].forEach(langprefix => {
      browser
        .resizeWindow(1024, 600)
        .smartURL(
          langprefix
            ? `/${langprefix}/contact/presuf_text`
            : "/contact/presuf_text"
        )
        .waitTillElementPresent(
          '[data-drupal-selector="edit-presuf-text-m"] td:first-child .tabledrag-handle',
          5000
        )
        .smartClick(
          '[name="presuf_text_m_add_more"]:not([disabled])',
          "mousedown"
        )
        .waitTillElementPresent('[name="presuf_text_m[1][value]"]')
        .smartClick(
          '[name="presuf_text_pre_m_add_more"]:not([disabled])',
          "mousedown"
        )
        .waitTillElementPresent('[name="presuf_text_pre_m[1][value]"]')
        .smartClick(
          '[name="presuf_text_suf_m_add_more"]:not([disabled])',
          "mousedown"
        )
        .waitTillElementPresent('[name="presuf_text_suf_m[1][value]"]')
        .smartClick(
          '[name="presuf_text_pre_suf_m_add_more"]:not([disabled])',
          "mousedown"
        )
        .waitTillElementPresent('[name="presuf_text_pre_suf_m[1][value]"]')
        .setValueAndChange('[name="presuf_text[0][value]"]', text)
        .setValueAndChange('[name="presuf_text_m[0][value]"]', text)
        .setValueAndChange('[name="presuf_text_pre[0][value]"]', text)
        .setValueAndChange('[name="presuf_text_pre_m[0][value]"]', text)
        .setValueAndChange('[name="presuf_text_suf[0][value]"]', text)
        .setValueAndChange('[name="presuf_text_suf_m[0][value]"]', text)
        .setValueAndChange('[name="presuf_text_pre_suf[0][value]"]', text)
        .setValueAndChange('[name="presuf_text_pre_suf_m[0][value]"]', text)
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
                ? "Text prefix suffix with inline errors"
                : "Text prefix suffix with simple errors"
            );
          }
        );
    });
  }
};
