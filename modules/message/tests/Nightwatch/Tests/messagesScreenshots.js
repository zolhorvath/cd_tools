/**
 * @file
 * Captures status messages with different states.
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
        .smartURL(langprefix ? `/${langprefix}/message` : "/message")
        .waitForElementPresent("[data-drupal-messages] .messages")
        .savefullScreenShot("01", langprefix, "Short Messages")
        .smartURL(langprefix ? `/${langprefix}/message/long` : "/message/long")
        .waitForElementPresent("[data-drupal-messages] .messages")
        .savefullScreenShot("02", langprefix, "Long Messages")
        .smartURL(langprefix ? `/${langprefix}/message/js` : "/message/js")
        .waitForElementPresent("[data-drupal-messages] .messages")
        .savefullScreenShot("03", langprefix, "JS Messages");
    });
  }
};
