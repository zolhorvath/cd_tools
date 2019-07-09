/**
 * @file
 * Captures title with different states.
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
  "Page title": function test(browser) {
    ["", "he"].forEach(langprefix => {
      browser
        .resizeWindow(1024, 600)
        .smartURL(
          langprefix
            ? `/${langprefix}/filter/tips/plain_text`
            : "/filter/tips/plain_text"
        )
        .waitForElementVisible(".shortcut-action__icon")
        .pause(250)
        .saveScreenShot("01", langprefix, "Page title without shortcut")
        .focusOn(".shortcut-action")
        .pause(500)
        .saveScreenShot("02", langprefix, "Page title without shortcut focused")
        .smartURL(langprefix ? `/${langprefix}/filter/tips` : "/filter/tips")
        .waitForElementVisible(".shortcut-action__icon")
        .pause(250)
        .saveScreenShot("03", langprefix, "Page title with shortcut")
        .focusOn(".shortcut-action")
        .pause(500)
        .saveScreenShot("04", langprefix, "Page title with shortcut focused");
    });
  }
};
