/**
 * @file
 * Captures action links.
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
  "Action links": function actionLinksTest(browser) {
    ["", "he"].forEach(langprefix => {
      browser
        .resizeWindow(1024, 600)
        .smartURL(langprefix ? `/${langprefix}/action-link` : "/action-link")
        .savefullScreenShot("01", langprefix, "Action links")
        .elements(
          "css selector",
          ".js-action-links-test .action-link",
          actionLinkQueryResult => {
            let index = 1;
            /* eslint-disable max-nested-callbacks */
            actionLinkQueryResult.value.forEach(elem => {
              browser.perform(done => {
                index += 1;
                browser.elementIdAttribute(
                  elem.ELEMENT,
                  "id",
                  actionLinkIdResult => {
                    if (actionLinkIdResult.value) {
                      browser
                        .focusOn(`#${actionLinkIdResult.value}`)
                        .pause(200)
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
  }
};
