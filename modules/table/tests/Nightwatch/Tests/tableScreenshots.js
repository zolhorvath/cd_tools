/**
 * @file
 * Captures tables.
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
  "Content form": function contentFormTest(browser) {
    ["", "he"].forEach(langprefix => {
      browser
        .resizeWindow(1024, 600)
        .smartURL(langprefix ? `/${langprefix}/table` : "/table")
        .waitTillElementPresent("table.test-table")
        .click('[name="table[3]"]')
        .moveToElement(
          "table.test-table tbody tr:nth-child(1) td:first-child",
          10,
          10
        )
        .click("table.test-table tbody tr:nth-child(1)")
        .savefullScreenShot("01", langprefix);
    });
  },
  "Field UI form": function fieldUiFormTest(browser) {
    ["", "he"].forEach(langprefix => {
      browser
        .resizeWindow(1024, 600)
        .smartURL(
          langprefix
            ? `/${langprefix}/admin/structure/types/manage/test_type/display`
            : "/admin/structure/types/manage/test_type/display"
        )
        .waitTillElementPresent("table.field-ui-overview")
        .savefullScreenShot("02", langprefix);
    });
  }
};
