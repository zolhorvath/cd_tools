/**
 * @file
 * Captures Fieldsets.
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
  Fieldset(browser) {
    ["", "he"].forEach(langprefix => {
      browser
        .resizeWindow(1024, 600)
        .smartURL(langprefix ? `/${langprefix}/fieldset` : "/fieldset")
        .click('[name="block[create]"]')
        .waitForElementVisible('[name="block[style][style_plugin]"]')
        .savefullScreenShot("01", langprefix);
    });
  }
};
