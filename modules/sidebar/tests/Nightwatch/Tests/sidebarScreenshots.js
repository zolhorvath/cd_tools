/**
 * @file
 * Captures entity meta sidebar with different states.
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
  'Sidebar'(browser) {
    'use strict';
    ['', 'he'].forEach((langprefix) => {
      // Node add form.
      browser
        .resizeWindow(1024, 600)
        .smartURL((langprefix ? '/' + langprefix : '') + '/node/add/cd')
        .waitForElementPresent('iframe.cke_wysiwyg_frame')
        .savefullScreenShot('01', langprefix, 'Sidebar default')
        // Expand details and leave focus there.
        .click('details.menu-link-form > summary')
        .click('details.path-form > summary')
        .click('details.node-form-author > summary')
        .click('details.node-form-author > summary')
        .click('details.node-form-options > summary')
        .focusOn('details.node-form-author > summary') // For Firefox.
        .pause(100)
        .saveScreenShot('02', langprefix, 'Sidebar expanded and focused')
        .savefullScreenShot('03', langprefix, 'Sidebar expanded')
        .click('nav[role="navigation"] li.is-active + li a')
        .waitForElementPresent('iframe.cke_wysiwyg_frame')
        .savefullScreenShot('04', langprefix, 'Sidebar edit default');
    });
  }
};
