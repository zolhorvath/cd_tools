/**
 * @file
 * Captures operations with different states.
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
  "Operations on content type manage": function operationsFieldUi(browser) {
    ["", "he"].forEach(langprefix => {
      browser
        .resizeWindow(1024, 600)
        // Test form.
        .smartURL(
          langprefix
            ? `/${langprefix}/admin/structure/types`
            : "/admin/structure/types"
        )
        .waitForElementPresent("tr:first-child li:first-child + li button")
        .perform(() => {
          browser.isVisible(
            ".tableresponsive-toggle-columns button",
            visibleToggle => {
              if (visibleToggle.status) {
                browser
                  .click(".tableresponsive-toggle-columns button")
                  .pause(100);
              }
            }
          );
        })
        .savefullScreenShot("01", langprefix)
        .focusOn("tr:first-child .dropbutton > li:first-child a")
        .pause(300)
        .savefullScreenShot("02", langprefix, "Manage fields focused")
        .focusOn("tr:first-child li:first-child + li button")
        .pause(300)
        .savefullScreenShot("03", langprefix, "Toggle focused")
        .click("tr:first-child li:first-child + li button")
        .pause(300)
        .savefullScreenShot("04", langprefix, "Multiple expanded")
        .focusOn(
          'tr:first-child .dropbutton a[href$="admin/structure/types/manage/cd/display"]'
        )
        .pause(300)
        .savefullScreenShot("05", langprefix, "Second subitem focused");
    });
  }
};
