/**
 * @file
 * Captures tabs with different states.
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
  Tabs(browser) {
    ["", "he"].forEach(langprefix => {
      let index = 0;
      let primaryExpanded = false;
      browser
        .resizeWindow(1024, 600)
        .smartURL(
          langprefix ? `/${langprefix}/tabs/plain_text` : "/tabs/plain_text"
        )
        .waitTillElementPresent('[aria-labelledby="primary-tabs-title"]')
        .waitTillElementPresent('[aria-labelledby="secondary-tabs-title"]')
        .perform(done => {
          index += 1;
          browser.savefullScreenShot(
            index.toString().padStart(2, "0"),
            langprefix,
            "Tabs default"
          );
          done();
        })
        .elements(
          "css selector",
          '[aria-labelledby="primary-tabs-title"]:not(.is-horizontal) [data-drupal-nav-tabs-trigger]',
          expandTriggerQueryResults => {
            // Toggle exist only on smaller browser windows.
            if (expandTriggerQueryResults.value.length) {
              browser.perform(done => {
                primaryExpanded = true;
                index += 1;
                browser
                  .click(
                    '[aria-labelledby="primary-tabs-title"] [data-drupal-nav-tabs-trigger]'
                  )
                  .pause(100)
                  .saveScreenShot(
                    index.toString().padStart(2, "0"),
                    langprefix,
                    "Toggler active"
                  );
                done();
              });
            }
          }
        )
        .elements(
          "css selector",
          '[aria-labelledby="primary-tabs-title"] a',
          tabLinkQueryResults => {
            tabLinkQueryResults.value.forEach(elem => {
              /* eslint-disable max-nested-callbacks */
              browser.perform(done => {
                index += 1;
                browser.elementIdAttribute(
                  elem.ELEMENT,
                  "data-drupal-link-system-path",
                  tabLinkIdQueryResult => {
                    if (tabLinkIdQueryResult.value) {
                      browser
                        .focusOn(
                          `[aria-labelledby="primary-tabs-title"] a[data-drupal-link-system-path="${
                            tabLinkIdQueryResult.value
                          }"]`
                        )
                        .pause(100)
                        .saveScreenShot(
                          index.toString().padStart(2, "0"),
                          langprefix,
                          "Primary tab link"
                        );
                    }
                  }
                );
                done();
              });
              /* eslint-enable max-nested-callbacks */
            });
          }
        )
        .perform(() => {
          if (primaryExpanded) {
            primaryExpanded = false;
            browser
              .click(
                '[aria-labelledby="primary-tabs-title"]:not(.is-horizontal) [data-drupal-nav-tabs-trigger]'
              )
              .pause(100);
          }
        })
        .elements(
          "css selector",
          '[aria-labelledby="secondary-tabs-title"] a',
          secondaryTabLinkQueryResults => {
            secondaryTabLinkQueryResults.value.forEach(elem => {
              /* eslint-disable max-nested-callbacks */
              browser.perform(done => {
                index += 1;
                browser.elementIdAttribute(
                  elem.ELEMENT,
                  "data-drupal-link-system-path",
                  secondaryTabLinkIdQueryResult => {
                    if (secondaryTabLinkIdQueryResult.value) {
                      browser
                        .focusOn(
                          `[aria-labelledby="secondary-tabs-title"] a[data-drupal-link-system-path="${
                            secondaryTabLinkIdQueryResult.value
                          }"]`
                        )
                        .pause(100)
                        .saveScreenShot(
                          index.toString().padStart(2, "0"),
                          langprefix,
                          "Secondary tab link"
                        );
                    }
                  }
                );
                done();
              });
              /* eslint-enable max-nested-callbacks */
            });
          }
        );
    });
  }
};
