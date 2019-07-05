/**
 * @file
 * Captures toolbar with different states.
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
  'Toolbar'(browser) {
    'use strict';
    ['', 'he'].forEach((langprefix) => {
      browser
        .resizeWindow(1024, 600)
        .smartURL((langprefix ? '/' + langprefix : '') + '/admin/structure/taxonomy')
        .execute(
          /* eslint-disable func-names, prefer-rest-params */
          function() {
            document.querySelector(".block-system-main-block").style.overflow = 'hidden';
          },
          /* eslint-enable func-names, prefer-rest-params */
        )
        .pause(100)
        .saveScreenShot('01', langprefix, 'Toolbar default') // Horizontal on desktop, or collapsed on narrow devices.
        .elements('css selector', '.toolbar-icon-toggle-vertical', (results) => {
          // Toolbar orientation toggle exist only on wider browser windows.
          if ((results.value.length)) {
            browser
              .click('.toolbar-icon-toggle-vertical')
              .waitForElementVisible('body.toolbar-vertical');
          }
          else {
            browser.click('#toolbar-item-administration');
          }
        })
        .pause(100)
        .waitForElementPresent('#toolbar-link-entity-taxonomy_vocabulary-collection.menu-item--active')
        .saveScreenShot('02', langprefix, 'Toolbar vertical')
        .click('#toolbar-link-system-admin_structure + button.open')
        .saveScreenShot('03', langprefix, 'Toolbar vertical with handle focus in some browsers')
        .click('#toolbar-link-system-admin_content + button:not(.open)')
        .saveScreenShot('04', langprefix, 'Toolbar second level handle')
        .click('#toolbar-link-toolbartest-dummy + button:not(.open)')
        .pause(100)
        .waitForElementVisible('#toolbar-link-toolbartest-dummy_child_1')
        .saveScreenShot('05', langprefix, 'Toolbar second level menu')
        .click('#toolbar-link-toolbartest-dummy_child_1')
        .elements('css selector', '#toolbar-item-administration:not(.is-active)', (results) => {
          if ((results.value.length)) {
            browser
              .click('#toolbar-item-administration:not(.is-active)');
          }
        })
        .pause(100)
        .waitForElementVisible('#toolbar-link-toolbartest-dummy_child_2')
        .focusOn('#toolbar-link-toolbartest-dummy_child_2')
        .saveScreenShot('06', langprefix, 'Toolbar states')
        // Restore horizontal orientation for hebrew test on desktop resolutions.
        .elements('css selector', '#toolbar-link-system-admin_content + button.open', (results) => {
          if ((results.value.length)) {
            browser
              .click('#toolbar-link-system-admin_content + button.open');
          }
        })
        .elements('css selector', '.toolbar-icon-toggle-horizontal', (results) => {
          if ((results.value.length)) {
            browser
              .click('.toolbar-icon-toggle-horizontal');
          }
        })
        .elements('css selector', '#toolbar-item-administration.is-active', (results) => {
          if ((results.value.length)) {
            browser
              .click('#toolbar-item-administration.is-active');
          }
        });
    });
  }
};
