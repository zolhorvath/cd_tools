/**
 * @file
 * Captures views exposed form with different states.
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
  Messages: function test(browser) {
    ["", "he"].forEach(langprefix => {
      browser
        .resizeWindow(1024, 600)
        .smartURL(
          langprefix ? `/${langprefix}/admin/content` : "/admin/content"
        )
        .waitForElementPresent('th input[type="checkbox"]')
        .savefullScreenShot(
          "01",
          langprefix,
          "Views exposed filter and bulk operations form"
        );
    });
  }
};
