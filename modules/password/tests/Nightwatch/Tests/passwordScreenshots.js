/**
 * @file
 * Captures password input test form with different states.
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
  Password(browser) {
    ["", "he"].forEach(langprefix => {
      browser
        .resizeWindow(1024, 600)
        .smartURL(langprefix ? `/${langprefix}/password` : "/password")
        .waitForElementPresent('[name="password_confirm[pass1]"]', 5000)
        .waitForElementPresent('[name="password_confirm[pass2]"]', 5000)
        .savefullScreenShot("01", langprefix, "Initial state")
        // Pw1.
        .setValue('[name="password_confirm[pass1]"]', "ascii")
        .pause(100)
        .waitForElementVisible('[name="password_confirm[pass2]"]', 5000)
        // Pw2.
        .setValue('[name="password_confirm[pass2]"]', "ascii")
        .savefullScreenShot("02", langprefix, "Zero but same")
        // Pw1.
        .setValue('[name="password_confirm[pass1]"]', "asc")
        .pause(1000)
        .savefullScreenShot("03", langprefix, "Weak")
        .setValue('[name="password_confirm[pass1]"]', "iias")
        .pause(1000)
        .savefullScreenShot("04", langprefix, "Fair")
        .setValue('[name="password_confirm[pass1]"]', "!")
        .pause(1000)
        .savefullScreenShot("05", langprefix, "Medium")
        .setValue('[name="password_confirm[pass1]"]', "E") // Uppercase utf8 doesn't mean an uppercase char?!
        .pause(1000)
        .savefullScreenShot("06", langprefix, "Strong")
        .setValue('[name="password_confirm[pass1]"]', "4")
        .pause(1000)
        .savefullScreenShot("07", langprefix, "Super")
        .click('[type="submit"]')
        .pause(100)
        .savefullScreenShot("08", langprefix, "Error");
    });
  }
};
