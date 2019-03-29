/**
 * @file
 * Captures buttons with different states.
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
  'Buttons'(browser) {
    'use strict';
    ['', 'he'].forEach((langprefix) => {
      browser
        .resizeWindow(1024, 600)
        .smartURL((langprefix ? '/' + langprefix : '') + '/buttons')
        .waitForElementVisible('.sbs-layout__region--main', 5000)
        .elements('css selector', '.sbs-layout__region--main .button', (result) => {
          let i = 2;
          result.value.forEach(elem => {
            browser.perform(done => {
              i++;
              browser.elementIdAttribute(elem.ELEMENT, 'id', result => {
                if (result.value) {
                  browser
                    .focusOn('#' + result.value)
                    .pause(100)
                    .saveScreenShot(i.toString().padStart(2, '0'), langprefix);
                }
              });
              done();
            });
          });
        })
        .savefullScreenShot('01', langprefix)
        .smartURL((langprefix ? '/' + langprefix : '') + '/buttons/disabled')
        .waitForElementVisible('.sbs-layout__region--main', 5000)
        .savefullScreenShot('02', langprefix);
    });
  }
};
