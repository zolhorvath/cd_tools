/**
 * @file
 * Captures pager with different states.
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
  Pager(browser) {
    ["", "he"].forEach(langprefix => {
      let index = 0;
      browser.resizeWindow(1024, 600);

      ["2", "286"].forEach(page => {
        browser
          .smartURL(
            langprefix
              ? `/${langprefix}/pager?page=${page}`
              : `/pager?page=${page}`
          )
          .waitForElementVisible(
            '[role="navigation"]:nth-of-type(2) .pager__items',
            5000
          )
          .perform(done => {
            index += 1;
            browser.savefullScreenShot(
              index.toString().padStart(2, "0"),
              langprefix
            );
            done();
          })
          .elements(
            "css selector",
            '[role="navigation"]:nth-of-type(2) a',
            pagerLinkQueryResults => {
              /* eslint-disable max-nested-callbacks */
              pagerLinkQueryResults.value.forEach(elem => {
                browser.perform(done => {
                  index += 1;
                  browser.elementIdAttribute(
                    elem.ELEMENT,
                    "title",
                    pagerLinkTitleQueryResult => {
                      if (pagerLinkTitleQueryResult.value) {
                        browser
                          .focusOn(
                            `[role="navigation"]:nth-of-type(2) li a[title="${
                              pagerLinkTitleQueryResult.value
                            }"]`
                          )
                          .pause(100)
                          .saveScreenShot(
                            index.toString().padStart(2, "0"),
                            langprefix
                          );
                      }
                    }
                  );
                  done();
                });
              });
              /* eslint-enable max-nested-callbacks */
            }
          );
      });
    });
  }
};
