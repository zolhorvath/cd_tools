/**
 * @file
 * Captures test vocabulary overview.
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
        .smartURL(langprefix ? `/${langprefix}/tabledrag` : "/tabledrag")
        .waitTillElementPresent(".tabledrag-toggle-weight-wrapper", 5000)
        .pause(100)
        // Make tabledrag handles visible if needed.
        .element("css selector", ".tabledrag-handle", tabledragHandleQuery => {
          browser.perform(done => {
            /* eslint-disable max-nested-callbacks */
            browser.elementIdDisplayed(
              tabledragHandleQuery.value.ELEMENT,
              handleIsDisplayedResult => {
                if (!handleIsDisplayedResult.value) {
                  browser.click(".tabledrag-toggle-weight-wrapper button");
                }
                done();
              }
            );
            /* eslint-enable max-nested-callbacks */
          });
        })
        .savefullScreenShot("01", langprefix)
        .click(".tabledrag-toggle-weight-wrapper button")
        .pause(100)
        .savefullScreenShot("02", langprefix);
    });
  }
};
