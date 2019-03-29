/**
 * @file
 * Captures tabs with different states.
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
  'Tabs'(browser) {
    'use strict';
    ['', 'he'].forEach((langprefix) => {
      let i = 0;
      let primary_expanded = false;
      browser
        .resizeWindow(1024, 600)
        .smartURL((langprefix ? '/' + langprefix : '') + '/tabs/plain_text')
        .waitTillElementPresent('[aria-labelledby="primary-tabs-title"]')
        .waitTillElementPresent('[aria-labelledby="secondary-tabs-title"]')
        .perform(done => {
          i++;
          browser.savefullScreenShot(i.toString().padStart(2, '0'), langprefix, 'Tabs default');
          done();
        })
        .elements('css selector', '[aria-labelledby="primary-tabs-title"]:not(.is-horizontal) [data-drupal-nav-tabs-trigger]', (results) => {
          // Toggle exist only on smaller browser windows.
          if ((results.value.length)) {
            browser.perform((done) => {
              primary_expanded = true;
              i++;
              browser
                .click('[aria-labelledby="primary-tabs-title"] [data-drupal-nav-tabs-trigger]')
                .pause(100)
                .saveScreenShot(i.toString().padStart(2, '0'), langprefix, 'Toggler active');
              done();
            });
          }
        })
        .elements('css selector', '[aria-labelledby="primary-tabs-title"] a', (result) => {
          result.value.forEach(elem => {
            browser.perform(done => {
              i++;
              browser.elementIdAttribute(elem.ELEMENT, 'data-drupal-link-system-path', result => {
                if (result.value) {
                  browser
                    .focusOn('[aria-labelledby="primary-tabs-title"] a[data-drupal-link-system-path="' + result.value + '"]')
                    .pause(100)
                    .saveScreenShot(i.toString().padStart(2, '0'), langprefix, 'Primary tab link');
                }
              });
              done();
            });
          });
        })
        .perform(done => {
          if (primary_expanded) {
            primary_expanded = false;
            browser
              .click('[aria-labelledby="primary-tabs-title"]:not(.is-horizontal) [data-drupal-nav-tabs-trigger]')
              .pause(100);
            done();
          }
          else {
            done();
          }
        })
        .elements('css selector', '[aria-labelledby="secondary-tabs-title"] a', (result) => {
          result.value.forEach(elem => {
            browser.perform(done => {
              i++;
              browser.elementIdAttribute(elem.ELEMENT, 'data-drupal-link-system-path', result => {
                if (result.value) {
                  browser
                    .focusOn('[aria-labelledby="secondary-tabs-title"] a[data-drupal-link-system-path="' + result.value + '"]')
                    .pause(100)
                    .saveScreenShot(i.toString().padStart(2, '0'), langprefix, 'Secondary tab link');
                }
              });
              done();
            });
          });
        });
    });
  }
};
