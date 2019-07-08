/**
 * @file
 * Captures buttons with different states.
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
  Buttons(browser) {
    ["", "he"].forEach(langprefix => {
      browser
        .resizeWindow(1024, 600)
        .smartURL(langprefix ? `/${langprefix}/buttons` : "/buttons")
        .waitForElementVisible(".sbs-layout__region--main", 5000)
        .elements(
          "css selector",
          ".sbs-layout__region--main .button",
          mainButtonsQueryResult => {
            let index = 2;
            /* eslint-disable max-nested-callbacks */
            mainButtonsQueryResult.value.forEach(elem => {
              browser.perform(done => {
                index += 1;
                browser.elementIdAttribute(
                  elem.ELEMENT,
                  "id",
                  buttonIdResult => {
                    if (buttonIdResult.value) {
                      browser
                        .focusOn(`#${buttonIdResult.value}`)
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
        )
        .savefullScreenShot("01", langprefix)
        .smartURL(
          langprefix ? `/${langprefix}/buttons/disabled` : "/buttons/disabled"
        )
        .waitForElementVisible(".sbs-layout__region--main", 5000)
        .savefullScreenShot("02", langprefix);
    });
  }
};
