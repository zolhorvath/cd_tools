/**
 * @file
 * Captures progress and throbbers with different states.
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
  'Progress'(browser) {
    'use strict';
    ['', 'he'].forEach((langprefix) => {
      browser
        .resizeWindow(1024, 600)
        .smartURL((langprefix ? '/' + langprefix : '') + '/progress')
        .waitForElementPresent('.ajax-progress.progress--small', 5000)
        .waitForElementPresent('.ajax-progress.progress', 5000)
        .savefullScreenShot('01', langprefix)
        .click('[name="button_visible"]').pause(400)
        .savefullScreenShot('02', langprefix)
        .smartURL((langprefix ? '/' + langprefix : '') + '/progress/fullscreen')
        .savefullScreenShot('03', langprefix);
    });
  }
};
