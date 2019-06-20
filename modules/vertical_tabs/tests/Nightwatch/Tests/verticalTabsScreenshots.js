/**
 * @file
 * Captures Vertical Tab component with different states.
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
  "Vertical Tabs": function test(browser) {
    ["", "he"].forEach(langprefix => {
      let testUrl;
      let mobileTest;
      browser
        .resizeWindow(1024, 600)
        .smartURL(langprefix ? `/${langprefix}` : "")
        .waitForElementPresent("a.js--vertical_tabs-test-link")
        .getAttribute("a.js--vertical_tabs-test-link", "href", testlinkQuery => {
          testUrl = testlinkQuery.value;
        })
        .perform(() => {
          browser.url(testUrl);
        })
        .waitForElementPresent("[data-vertical-tabs-panes]")
        .pause(100)
        .perform(() => {
          browser.elements(
            "css selector",
            ".js-form-type-vertical-tabs > div > ul",
            verticalTabMenuQuery => {
              mobileTest = verticalTabMenuQuery.value.length < 1;
            }
          );
        })
        .savefullScreenShot("01", langprefix, "Vertical Tab default")
        .moveToElement("[data-vertical-tabs-panes]", 0, -20)
        .perform(() => {
          if (mobileTest) {
            browser
              // Focus the (first) active tab.
              .click("[data-vertical-tabs-panes] details:first-child summary")
              .focusOn("[data-vertical-tabs-panes] details:first-child summary")
              .pause(250)
              .saveScreenShot("02", langprefix, "First active tab focused")
              .click(
                "[data-vertical-tabs-panes] details#edit-visibility-request-path summary"
              )
              .pause(250)
              .moveToElement(
                "[data-vertical-tabs-panes] details#edit-visibility-request-path",
                0,
                -20
              )
              .focusOn(
                "[data-vertical-tabs-panes] details#edit-visibility-request-path summary"
              )
              .pause(250)
              .saveScreenShot("03", langprefix, "Active path tab focused")
              .click(
                "[data-vertical-tabs-panes] details#edit-visibility-request-path summary"
              )
              .focusOn(
                "[data-vertical-tabs-panes] details#edit-visibility-user-role summary"
              )
              .pause(250)
              .saveScreenShot("04", langprefix, "Inactive roles tab focused")
              .smartURL(langprefix ? `/${langprefix}` : "")
              .url(`${testUrl}#edit-visibility-request-path`)
              .pause(250)
              // .waitForElementVisible(
              //   '[data-vertical-tabs-panes] details#edit-visibility-request-path [name="visibility[request_path][pages]"]',
              //   5000
              // )
              .savefullScreenShot(
                "05",
                langprefix,
                "Path tab should be active"
              );
          } else {
            browser
              // Focus the (first) active tab.
              .focusOn('.js-form-item .is-selected a[href^="#"]')
              .pause(250)
              .saveScreenShot("02", langprefix, "First active tab focused")
              .click('a[href="#edit-visibility-request-path"]')
              .focusOn('a[href="#edit-visibility-request-path"]')
              .pause(250)
              .saveScreenShot("03", langprefix, "Active path tab focused")
              .focusOn('a[href="#edit-visibility-user-role"]')
              .pause(250)
              .saveScreenShot("04", langprefix, "Inactive roles tab focused")
              // Test that url with fragment opens the right vertical tabs content.
              .smartURL(langprefix ? `/${langprefix}` : "")
              .url(`${testUrl}#edit-visibility-request-path`)
              .pause(250)
              // .waitForElementVisible(
              //   '#edit-visibility-request-path [name="visibility[request_path][pages]"]',
              //   5000
              // )
              .savefullScreenShot(
                "05",
                langprefix,
                "Path tab should be active"
              );
          }
        });
    });
  }
};
