/**
 * @file
 * Captures checkbox radio testform with different states.
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
  "Checked states": function checkedStatesTest(browser) {
    ["", "he"].forEach(langprefix => {
      browser
        .resizeWindow(1024, 600)
        .smartURL(
          langprefix
            ? `/${langprefix}/contact/checkbox_radio`
            : "/contact/checkbox_radio"
        )
        .waitTillElementPresent(
          '[name="form_id"][value="contact_message_checkbox_radio_form"]'
        )
        .click('[name="checkbox[value]"]')
        .click('[name="checkboxes[first]"]')
        .click('[name="checkboxes[second]"]')
        .click('[name="checkboxes[third]"]')
        .click('[name="radios"][value="second"]')
        .pause(300)
        .savefullScreenShot("01", langprefix);
    });
  },
  "Error states": function errorStatesTest(browser) {
    ["", "he"].forEach(langprefix => {
      browser
        .resizeWindow(1024, 600)
        .smartURL(
          langprefix
            ? `/${langprefix}/contact/checkbox_radio`
            : "/contact/checkbox_radio"
        )
        .waitTillElementPresent(
          '[name="form_id"][value="contact_message_checkbox_radio_form"]'
        )
        .click('[name="checkbox[value]"]')
        .click('[name="checkboxes[first]"]')
        .click('[name="checkboxes[second]"]')
        .click('[name="checkboxes[third]"]')
        .submitForm("form.contact-message-checkbox-radio-form")
        .waitTillElementPresent(".messages--error", 5000)
        .elements(
          "css selector",
          ".form-item__error-message, .form-item--error-message, .fieldset__error-message",
          inlineFormMessagesQueryResults => {
            browser.savefullScreenShot(
              inlineFormMessagesQueryResults.value.length ? "03" : "02",
              langprefix,
              inlineFormMessagesQueryResults.value.length
                ? "Inline error states"
                : ""
            );
          }
        );
    });
  }
};
