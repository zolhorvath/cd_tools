/**
 * @file
 * Captures Details component with different states.
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
  Details(browser) {
    ["", "he"].forEach(langprefix => {
      browser
        .resizeWindow(1024, 600)
        .smartURL(
          langprefix
            ? `/${langprefix}/admin/config/system/site-information`
            : "/admin/config/system/site-information"
        )
        .waitForElementPresent("#edit-site-information[open] > summary")
        .savefullScreenShot("01", langprefix, "Details default")
        // Collapse site info details and leave focus there.
        .click("#edit-site-information[open] > summary")
        .waitTillElementPresent("#edit-site-information:not([open]) > summary")
        .focusOn("#edit-site-information:not([open]) > summary") // For Firefox.
        .pause(100)
        .saveScreenShot("02", langprefix, "Details closed")
        // Expand it again.
        .click("#edit-site-information:not([open]) > summary")
        .waitTillElementPresent("#edit-site-information[open] > summary")
        .focusOn("#edit-site-information[open] > summary") // For Firefox.
        .pause(100)
        .saveScreenShot("03", langprefix, "Details expanded again");
    });
  }
};
