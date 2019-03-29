/**
 * @file
 * Captures text input testform with different states.
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
  'Filled form'(browser) {
    'use strict';
    const path = require('path');
    const filePath = path.join(__dirname, '..', '..', 'assets', 'test.txt');
    const imagePath = path.join(__dirname, '..', '..', 'assets', 'test.gif');
    const fileInvalidPath = path.join(__dirname, '..', '..', 'assets', 'testBig.txt');
    const imageInvalidPath = path.join(__dirname, '..', '..', 'assets', 'test.png');
    let fileTest = true; // See first perform command.

    ['', 'he'].forEach((langprefix) => {
      browser
        .perform(() => {
          // Skip file upload tests on mobileEmulation since it's not supported.
          const platformName = (browser.capabilities.platformName || browser.capabilities.platform || 'nan').toLowerCase();
          const browserName = (browser.capabilities.browserName || 'nan').toLowerCase();
          const unsupported = ['chrome:android', 'safari:ios'];
          fileTest = unsupported.indexOf(`${browserName}:${platformName}`) < 0;
        })
        .resizeWindow(1024, 600)
        .smartURL((langprefix ? '/' + langprefix : '') + '/contact/textform')
        // Text.
        .setValueAndChange('[name="text[0][value]"]', 'Some text with error')
        // Email.
        .setValueAndChange('[name="email[0][value]"]', 'test.error@localhost')
        // Phone.
        .setValueAndChange('[name="phone[0][value]"]', '0123356789')
        // Number.
        .setValueAndChange('[name="number[0][value]"]', '33')
        // Timestamp: providing invalid value.
        .setValueAndChange('[name="timestamp[0][value][date]"]', '1980-11-11')
        .setValueAndChange('[name="timestamp[0][value][time]"]', '12:00:00')
        // Search.
        .setValueAndChange('[name="search[0][value]"]', 'search for error')
        // Password.
        .setValueAndChange('[name="password[0][value]"]', 'error error')
        // Date.
        .setValueAndChange('[name="date[0][value][date]"]', '2018-11-06')
        // Date and Time, filling only date.
        .setValueAndChange('[name="datetime[0][value][date]"]', '2018-11-06')
        // Datetime range: starting datetime is more than the ending datetime.
        .setValueAndChange('[name="daterange[0][value][date]"]', '2018-12-01')
        .setValueAndChange('[name="daterange[0][value][time]"]', '12:00:00')
        .setValueAndChange('[name="daterange[0][end_value][date]"]', '2018-10-28')
        .setValueAndChange('[name="daterange[0][end_value][time]"]', '11:00:00')
        // Datetime range - all day.
        .setValueAndChange('[name="daterange_ad[0][value][date]"]', '2018-12-01')
        .setValueAndChange('[name="daterange_ad[0][end_value][date]"]', '2018-10-28')
        // Color.
        .setValueAndChange('[name="color[0][value]"]', '#ff0000')
        .perform(() => {
          if (fileTest) {
            // Valid image file.
            browser
              .setValueAndChange('[name="files[image_0]"]:not([disabled])', imagePath)
              .waitTillElementPresent('[name="image[0][alt]"]', 5000)
              .setValueAndChange('[name="image[0][alt]"]', 'Green square')
              // Valid file.
              .setValueAndChange('[name="files[file_0]"]:not([disabled])', filePath)
              .waitTillElementPresent('[name="file[0][description]"]', 5000)
              .setValueAndChange('[name="file[0][description]"]', 'File description');
          }
        })
        .savefullScreenShot('01', langprefix)
        .click('input#edit-submit')
        // Waiting for error messages (Big Pipe).
        .waitTillElementPresent('.messages--error', 5000)
        .perform(() => {
          if (fileTest) {
            browser
              // Remove image and file.
              .click('[name="file_0_remove_button"]')
              .click('[name="image_0_remove_button"]')
              .waitTillElementPresent('[name="files[file_0]"]:not([disabled])', 5000)
              // Upload invalid file.
              .setValueAndChange('[name="files[file_0]"]', fileInvalidPath)
              .waitTillElementPresent('[name="files[file_0]"].error', 5000)
              // Upload invalid image.
              .waitTillElementPresent('[name="files[image_0]"]:not([disabled])', 5000)
              .setValueAndChange('[name="files[image_0]"]', imageInvalidPath)
              .waitTillElementPresent('[name="files[image_0]"].error', 5000);
          }
        })
        // Create 'Error' screenshot.
        .elements('css selector', '.form-item__error-message,.form-item--error-message', (results) => {
          browser.savefullScreenShot((results.value.length ? '03' : '02'), langprefix, (results.value.length ? 'Inline error states' : 'Error states'));
        });
    });
  }
};
