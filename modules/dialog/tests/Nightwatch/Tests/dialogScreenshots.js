/**
 * @file
 * Captures different type of dialogs.
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
  Dialogs: function test(browser) {
    ["", "he"].forEach(langprefix => {
      let index = 0;
      browser
        .resizeWindow(1024, 600)
        .smartURL(langprefix ? `/${langprefix}/dialog` : "/dialog")
        .waitForElementPresent(".js [data-dialog-type]")
        .perform(() => {
          index += 1;
          const autoSelectors = {
            ".js-dialog-button": {
              title: "Auto-sized dialog",
              pause: 100
            },
            ".js-modal-dialog-button": {
              title: "Auto-sized modal dialog",
              pause: 100
            },
            ".js-offcanvas-dialog-button": {
              title: "Auto-sized offcanvas dialog",
              pause: 2000
            },
            ".js-offcanvas-top-dialog-button": {
              title: "Auto-sized offcanvas-top dialog",
              pause: 2000
            },
            ".js-dialog-sized-button": {
              title: "Dimensioned dialog",
              pause: 100
            },
            ".js-modal-dialog-sized-button": {
              title: "Dimensioned modal dialog",
              pause: 100
            },
            ".js-offcanvas-dialog-sized-button": {
              title: "Dimensioned offcanvas dialog",
              pause: 2000
            },
            ".js-offcanvas-top-dialog-sized-button": {
              title: "Dimensioned offcanvas-top dialog",
              pause: 2000
            }
          };
          Object.keys(autoSelectors).forEach(selector => {
            browser
              .click(selector)
              .pause(100)
              .waitForElementPresent('[role="dialog"]')
              .pause(autoSelectors[selector].pause)
              .saveScreenShot(
                index.toString().padStart(2, "0"),
                langprefix,
                autoSelectors[selector].title
              )
              .click('[role="dialog"] button[title]')
              .pause(autoSelectors[selector].pause);
          });
        });
    });
  }
};
