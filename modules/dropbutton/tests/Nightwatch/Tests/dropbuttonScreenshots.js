/**
 * @file
 * Captures drop buttons with different states.
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
  'Dropbutton'(browser) {
    'use strict';
    ['', 'he'].forEach((langprefix) => {
      // Node add form.
      browser
        .resizeWindow(1024, 600)
        .smartURL((langprefix ? '/' + langprefix : '') + '/admin/config/regional/language')
        .savefullScreenShot('01', langprefix)

        // Dropbuttons in table.
        .focusOn('[data-drupal-selector="edit-languages-en-operations-data"] li:first-child a')
        .pause(300)
        .saveScreenShot('02', langprefix, 'Single focused')
        .focusOn('[data-drupal-selector="edit-languages-he-operations-data"] li:first-child a')
        .pause(300)
        .saveScreenShot('03', langprefix, 'Multiple focused')
        .click('[data-drupal-selector="edit-languages-he-operations-data"] li:first-child + li button')
        .pause(300)
        .saveScreenShot('04', langprefix, 'Multiple expanded')
        .focusOn('[data-drupal-selector="edit-languages-he-operations-data"] li:first-child + li + li a')
        .pause(300)
        .saveScreenShot('05', langprefix, 'Multiple expanded focused')

        // Dropbutton with inputs, in form actions.
        .focusOn('[data-drupal-selector="edit-group-1"] li:first-child .button')
        .pause(300)
        .saveScreenShot('06', langprefix, 'Single input focused')
        .focusOn('[data-drupal-selector="edit-group-2"] li:first-child .button')
        .pause(300)
        .saveScreenShot('07', langprefix, 'Multiple input focused')
        .click('[data-drupal-selector="edit-group-2"] li:nth-child(2) button')
        .pause(300)
        .saveScreenShot('08', langprefix, 'Multiple input expanded')
        .focusOn('[data-drupal-selector="edit-languages-he-operations-data"] li:nth-child(3) .button')
        .pause(300)
        .saveScreenShot('09', langprefix, 'Multiple input expanded focused')

        // Dropbutton with links, in form actions.
        .focusOn('[data-drupal-selector="edit-dropbutton-single"] li:first-child a')
        .pause(300)
        .saveScreenShot('10', langprefix, 'Single link in actions focused')
        .focusOn('[data-drupal-selector="edit-dropbutton"] li:first-child a')
        .pause(300)
        .saveScreenShot('11', langprefix, 'Multiple links in actions focused')
        .click('[data-drupal-selector="edit-dropbutton"] li:nth-child(2) button')
        .pause(300)
        .saveScreenShot('12', langprefix, 'Multiple links in actions expanded')
        .focusOn('[data-drupal-selector="edit-dropbutton"] li:nth-child(3) a')
        .pause(300)
        .saveScreenShot('13', langprefix, 'Multiple links in actions focused');
    });
  }
};
