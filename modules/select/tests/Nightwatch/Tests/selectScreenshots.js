/**
 * @file
 * Captures select test form with different states.
 *
 * Error state's fileName vary by the state of the inline_form_errors module.
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
  'Select form'(browser) {
    'use strict';
    ['', 'he'].forEach((langprefix) => {
      browser
        .resizeWindow(1024, 600)
        .smartURL((langprefix ? '/' + langprefix : '') + '/contact/select')
        .click('[name="select_single"]')
        .click('[name="select_single"] option[value="error"]')
        .click('[name="select_multiple[]"] option[value="error"]')
        .pause(300)
        .savefullScreenShot('01', langprefix)
        .click('input#edit-submit')
        .waitTillElementPresent('.messages--error', 5000)
        .elements('css selector', '.form-item__error-message,.form-item--error-message', (results) => {
          browser.savefullScreenShot((results.value.length ? '03' : '02'), langprefix, (results.value.length ? 'Select form with inline errors' : 'Select form with errors'));
        });
    });
  }
};
