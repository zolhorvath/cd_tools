/**
 * @file
 * Captures pager with different states.
 */
module.exports = {
  '@tags': ['claro'],
  before(browser) {
    'use strict';
    if (browser.drupalInstall) {
      browser.drupalInstall({
        installProfile: 'clarodist'
      });
    }
  },
  after(browser) {
    'use strict';
    if (browser.drupalUninstall) {
      browser.drupalUninstall().end();
    }
    else {
      browser.end();
    }
  },
  'Pager'(browser) {
    'use strict';
    ['', 'he'].forEach((langprefix) => {
      let i = 0;
      browser
        .resizeWindow(1024, 600);

      ['2', '286'].forEach((page) => {
        browser
          .smartURL((langprefix ? '/' + langprefix : '') + '/pager?page=' + page)
          .waitForElementVisible('[aria-labelledby="pagination-heading--2"] .pager__items', 5000)
          .perform(done => {
            i++;
            browser.savefullScreenShot(i.toString().padStart(2, '0'), langprefix);
            done();
          })
          .elements('css selector', '[aria-labelledby="pagination-heading--2"] a', (result) => {
            result.value.forEach(elem => {
              browser.perform(done => {
                i++;
                browser.elementIdAttribute(elem.ELEMENT, 'title', result => {
                  if (result.value) {
                    browser
                      .focusOn('[aria-labelledby="pagination-heading--2"] li a[title="' + result.value + '"]')
                      .pause(100)
                      .saveScreenShot(i.toString().padStart(2, '0'), langprefix);
                  }
                });
                done();
              });
            });
          });
      });
    });
  }
};
