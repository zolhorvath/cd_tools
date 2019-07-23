/**
 * @file
 * Captures drop buttons with different states.
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
  Dropbutton(browser) {
    ["", "he"].forEach(langprefix => {
      browser
        .resizeWindow(1024, 600)
        // Operations test form.
        .smartURL(
          langprefix
            ? `/${langprefix}/dropbutton/operations`
            : "/dropbutton/operations"
        )
        .waitForElementPresent(
          ".form-actions .dropbutton-multiple:last-of-type"
        )
        .savefullScreenShot("01", langprefix)
        // Dropbuttons in table.
        .focusOn(
          '[data-drupal-selector="edit-languages-en-operations-data"] li:first-child a'
        )
        .pause(300)
        .saveScreenShot("02", langprefix, "Single focused")
        .focusOn(
          '[data-drupal-selector="edit-languages-he-operations-data"] li:first-child a'
        )
        .pause(300)
        .saveScreenShot("03", langprefix, "Multiple focused")
        .click(
          '[data-drupal-selector="edit-languages-he-operations-data"] li:first-child + li button'
        )
        .pause(300)
        .saveScreenShot("04", langprefix, "Multiple expanded")
        .focusOn(
          '[data-drupal-selector="edit-languages-he-operations-data"] li a[href*="admin/config/regional/language/delete/he"]'
        )
        .pause(300)
        .saveScreenShot("05", langprefix, "Multiple expanded focused")
        // Dropbutton with inputs, in form actions.
        .focusOn(
          '[data-drupal-selector="edit-group-1"] li:first-child input[type="submit"]'
        )
        .pause(300)
        .saveScreenShot("06", langprefix, "Single input focused")
        .focusOn(
          '.dropbutton[data-drupal-selector="edit-group-2"] li:first-child input[type="submit"]'
        )
        .pause(300)
        .saveScreenShot("07", langprefix, "Multiple input focused")
        .click(
          '.dropbutton[data-drupal-selector="edit-group-2"] li:nth-child(2) button'
        )
        .pause(300)
        .saveScreenShot("08", langprefix, "Multiple input expanded")
        .focusOn(
          '.dropbutton[data-drupal-selector="edit-group-2"] input[type="submit"][data-name="three"]'
        )
        .pause(300)
        .saveScreenShot("09", langprefix, "Multiple input expanded focused")
        // Dropbutton with links, in form actions.
        .focusOn(
          '[data-drupal-selector="edit-dropbutton-single"] li:first-child a'
        )
        .pause(300)
        .saveScreenShot("10", langprefix, "Single link in actions focused")
        .focusOn('[data-drupal-selector="edit-dropbutton"] li:first-child a')
        .pause(300)
        .saveScreenShot("11", langprefix, "Multiple links in actions focused")
        .click(
          '[data-drupal-selector="edit-dropbutton"] li:nth-child(2) button'
        )
        .pause(300)
        .saveScreenShot("12", langprefix, "Multiple links in actions expanded")
        .focusOn(
          '[data-drupal-selector="edit-dropbutton"] a[data-name="three"]'
        )
        .pause(300)
        .saveScreenShot("13", langprefix, "Multiple links in actions focused")

        // Views UI view edit test form.
        .smartURL(
          langprefix ? `/${langprefix}/dropbutton-views` : "/dropbutton-views"
        )
        .waitForElementPresent("#views-display-top .dropbutton-multiple")
        .click(
          "#edit-displays-settings-settings-content-tab-content-details-columns-third > summary"
        )
        .savefullScreenShot("14", langprefix, "Views UI dropbuttons");
    });
  }
};
