/**
 * @file
 * Captures password input test form with different states.
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
  'Password'(browser) {
    'use strict';
    ['', 'he'].forEach((langprefix) => {
      browser
        .resizeWindow(1024, 600)
        .smartURL((langprefix ? '/' + langprefix : '') + '/password')
        .waitForElementPresent('[name="password_confirm[pass1]"]', 5000)
        .waitForElementPresent('[name="password_confirm[pass2]"]', 5000)
        // Pw1.
        .setValue('[name="password_confirm[pass1]"]', 'ascii')
        // Pw2.
        .setValue('[name="password_confirm[pass2]"]', 'ascii')
        .savefullScreenShot('01', langprefix, 'Zero but same')
        // Pw1.
        .setValue('[name="password_confirm[pass1]"]', 'asc').pause(1000)
        .savefullScreenShot('02', langprefix, 'Weak')
        .setValue('[name="password_confirm[pass1]"]', 'iias').pause(1000)
        .savefullScreenShot('03', langprefix, 'Fair')
        .setValue('[name="password_confirm[pass1]"]', '!').pause(1000)
        .savefullScreenShot('04', langprefix, 'Medium')
        .setValue('[name="password_confirm[pass1]"]', 'E').pause(1000) // Uppercase utf8 doesn't mean an uppercase char?!
        .savefullScreenShot('05', langprefix, 'Strong')
        .setValue('[name="password_confirm[pass1]"]', '4').pause(1000)
        .savefullScreenShot('06', langprefix, 'Super');
    });
  }
};
