/**
 * @file
 * Captures formatted text widget with prefix and / or suffix.
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
  "Formatted text prefix suffix": function test(browser) {
    const text = "A relatively longer sentence with a small error.";
    ["", "he"].forEach(langprefix => {
      browser
        .resizeWindow(1024, 600)
        .smartURL(
          langprefix
            ? `/${langprefix}/contact/presuf_formatted`
            : "/contact/presuf_formatted"
        )
        .waitTillElementPresent(
          '[data-drupal-selector="edit-presuf-formatted-m"] td:first-child .tabledrag-handle',
          5000
        )
        .click('[name="presuf_formatted_m_add_more"]:not([disabled])')
        .click('[name="presuf_formatted_pre_m_add_more"]:not([disabled])')
        .click('[name="presuf_formatted_suf_m_add_more"]:not([disabled])')
        .click('[name="presuf_formatted_pre_suf_m_add_more"]:not([disabled])')
        .waitTillElementPresent(
          '[name="presuf_formatted_m_add_more"]:not([disabled])'
        )
        .waitTillElementPresent(
          '[name="presuf_formatted_pre_m_add_more"]:not([disabled])'
        )
        .waitTillElementPresent(
          '[name="presuf_formatted_suf_m_add_more"]:not([disabled])'
        )
        .waitTillElementPresent(
          '[name="presuf_formatted_pre_suf_m_add_more"]:not([disabled])'
        )
        .setValueAndChange('[name="presuf_formatted[0][value]"]', text)
        .setValueAndChange('[name="presuf_formatted_m[0][value]"]', text)
        .setValueAndChange('[name="presuf_formatted_pre[0][value]"]', text)
        .setValueAndChange('[name="presuf_formatted_pre_m[0][value]"]', text)
        .setValueAndChange('[name="presuf_formatted_suf[0][value]"]', text)
        .setValueAndChange('[name="presuf_formatted_suf_m[0][value]"]', text)
        .setValueAndChange('[name="presuf_formatted_pre_suf[0][value]"]', text)
        .setValueAndChange(
          '[name="presuf_formatted_pre_suf_m[0][value]"]',
          text
        )
        .savefullScreenShot("01", langprefix)
        .click("#edit-submit")
        .waitTillElementPresent("[data-drupal-messages] div", 5000)
        .elements(
          "css selector",
          ".form-item__error-message, .form-item--error-message, .fieldset__error-message",
          inlineFormMessagesQueryResults => {
            browser.savefullScreenShot(
              inlineFormMessagesQueryResults.value.length ? "03" : "02",
              langprefix,
              inlineFormMessagesQueryResults.value.length
                ? "Formatted text prefix suffix with inline errors"
                : "Formatted text prefix suffix with simple errors"
            );
          }
        );
    });
  }
};
