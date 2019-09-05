/**
 * @file
 * Captures field cardinality testform with different states.
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
  "Field Cardinality": function test(browser) {
    ["", "he"].forEach(langprefix => {
      browser
        .resizeWindow(1024, 600)
        .smartURL(
          langprefix
            ? `/${langprefix}/contact/field_cardinality_test`
            : "/contact/field_cardinality_test"
        )
        .waitTillElementPresent(".tabledrag-toggle-weight-wrapper", 5000)
        .pause(100)
        // Make tabledrag handles visible if needed.
        .element("css selector", ".tabledrag-handle", tabledragHandleQuery => {
          browser.perform(done => {
            /* eslint-disable max-nested-callbacks */
            browser.elementIdDisplayed(
              tabledragHandleQuery.value.ELEMENT,
              handleIsDisplayedResult => {
                if (!handleIsDisplayedResult.value) {
                  browser.click(".tabledrag-toggle-weight-wrapper button");
                }
                done();
              }
            );
            /* eslint-enable max-nested-callbacks */
          });
        })
        .setValueAndChange(
          '[name="multitext_unlimited[0][value]"]',
          "Some text with error"
        )
        .setValueAndChange(
          '[name="multitext_unlimited_required[0][value]"]',
          "Some text"
        )
        .focusOn('[name="multitext_unlimited_required_add_more"]')
        .click('[name="multitext_unlimited_required_add_more"]')
        .waitTillElementPresent(
          '[name="multitext_unlimited_required[2][value]"]',
          5000
        )
        .setValueAndChange(
          '[name="multitext_unlimited_required[2][value]"]',
          "Some text with error"
        )
        .setValueAndChange(
          '[name="multitext_limited[0][value]"]',
          "Some text here as well"
        )
        .setValueAndChange(
          '[name="multitext_limited[2][value]"]',
          "Trigger an error"
        )
        .savefullScreenShot("01", langprefix)
        // Hide drag handles.
        .click(".tabledrag-toggle-weight-wrapper button")
        .savefullScreenShot("02", langprefix)
        // Show them again
        .click(".tabledrag-toggle-weight-wrapper button")
        .focusOn("input#edit-submit")
        .click("input#edit-submit")
        // Waiting for error messages (Big Pipe).
        .waitTillElementPresent(".messages--error", 5000)
        // Create 'Error' screenshot.
        .elements(
          "css selector",
          ".form-item__error-message, .form-item--error-message",
          inlineFormMessagesQueryResults => {
            browser.perform(done => {
              browser.savefullScreenShot(
                inlineFormMessagesQueryResults.value.length ? "05" : "03",
                langprefix,
                inlineFormMessagesQueryResults.value.length
                  ? "Inline error states with handles"
                  : "Error states with handles"
              );
              done();
            });
          }
        )
        // Hide drag handles.
        .click(".tabledrag-toggle-weight-wrapper button")
        .elements(
          "css selector",
          ".form-item__error-message, .form-item--error-message",
          inlineFormMessagesQueryResults => {
            browser.perform(done => {
              browser.savefullScreenShot(
                inlineFormMessagesQueryResults.value.length ? "06" : "04",
                langprefix,
                inlineFormMessagesQueryResults.value.length
                  ? "Inline error states without handles"
                  : "Error states without handles"
              );
              done();
            });
          }
        );
    });
  }
};
