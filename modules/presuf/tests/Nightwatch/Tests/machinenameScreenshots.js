/**
 * @file
 * Captures file widget with different states.
 *
 * Error state's fileName vary by the state of the inline_form_errors module.
 */

const path = require("path");

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
        .savefullScreenShot("03", langprefix);
    });
  }
};
