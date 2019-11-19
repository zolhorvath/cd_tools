/**
 * @file
 * Captures machine name widgets with different states.
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
  "Machine Name": function test(browser) {
    ["", "he"].forEach(langprefix => {
      browser
        .resizeWindow(1024, 600)
        .smartURL(
          langprefix
            ? `/${langprefix}/admin/structure/display-modes/form/add/contact_message`
            : "/admin/structure/display-modes/form/add/contact_message"
        )
        .waitTillElementPresent('[name="label"].machine-name-source', 5000)
        .savefullScreenShot("01", langprefix)
        .setValueAndChange('[name="label"].machine-name-source', "Default")
        .waitForElementVisible(
          '[name="label"].machine-name-source ~ * .admin-link .link',
          5000
        )
        .savefullScreenShot("02", langprefix)
        .click('[name="label"].machine-name-source ~ * .admin-link .link')
        .waitTillElementPresent(
          ".js-form-type-machine-name:not(.visually-hidden)"
        )
        .savefullScreenShot("03", langprefix)
        .submitForm("form.entity-form-mode-add-form")
        .waitTillElementPresent("[data-drupal-messages] div", 5000)
        .elements(
          "css selector",
          ".form-item__error-message, .form-item--error-message, .fieldset__error-message",
          inlineFormMessagesQueryResults => {
            browser.savefullScreenShot(
              inlineFormMessagesQueryResults.value.length ? "05" : "04",
              langprefix,
              inlineFormMessagesQueryResults.value.length
                ? "Machine Name with inline errors"
                : "Machine Name with simple errors"
            );
          }
        );
    });
  }
};
