/**
 * @file
 * Captures text area testform with different states.
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
  'Textarea form'(browser) {
    'use strict';
    ['', 'he'].forEach((langprefix) => {
      browser
        .resizeWindow(1024, 600)
        .smartURL((langprefix ? '/' + langprefix : '') + '/contact/textarea')
        .setValueAndChange('[name="message[0][value]"]', 'Test message body with error')
        .waitTillElementPresent('[name="formatted[0][value]"] + .cke .cke_wysiwyg_frame', 5000)
        .click('[name="formatted[0][format]"]')
        .click('[name="formatted[0][format]"] option[value="no_editor"]')
        .click('[name="message[0][value]"]')
        .waitForElementNotPresent('[name="formatted[0][value]"] + .cke', 5000)
        .setValueAndChange('[name="formatted[0][value]"]', 'Some text here...')
        .click('[class*="js-form-item-"][class*="-formatted-summary-0-value"] .link-edit-summary')
        .waitForElementVisible('[name="formatted_summary[0][summary]"]', 5000)
        .setValueAndChange('[name="formatted_summary[0][summary]"]', '..some summary here, but no text in main area!')
        .pause(300)
        .savefullScreenShot('01', langprefix)
        .click('input#edit-submit')
        .waitTillElementPresent('.messages--error', 5000)
        .elements('css selector', '.form-item__error-message,.form-item--error-message', (results) => {
          browser.savefullScreenShot((results.value.length ? '03' : '02'), langprefix, (results.value.length ? 'Inline errors' : 'Errors'));
        });
    });
  }
};
