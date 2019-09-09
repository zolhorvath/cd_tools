/**
 * @file
 * Captures file widget with different states.
 *
 * Error state's fileName vary by the state of the inline_form_errors module.
 */

const path = require("path");

module.exports = {
  "@tags": ["claro"],
  before(browser) {
    if (browser.drupalInstall) {
      browser.drupalInstall({
        installProfile: "clarodist"
      });
    }
  },
  after(browser) {
    if (browser.drupalUninstall) {
      browser.drupalUninstall().end();
    } else {
      browser.end();
    }
  },
  "File Widgets": function test(browser) {
    const filePath = path.join(__dirname, "..", "..", "assets", "test.txt");
    const fileInvalidPath = path.join(
      __dirname,
      "..",
      "..",
      "assets",
      "test-big.txt"
    );
    let fileTest = true; // See first perform command.

    ["", "he"].forEach(langprefix => {
      browser
        .perform(() => {
          // Skip file upload tests on mobileEmulation since it's not supported.
          const platformName = (
            browser.capabilities.platformName ||
            browser.capabilities.platform ||
            "nan"
          ).toLowerCase();
          const browserName = (
            browser.capabilities.browserName || "nan"
          ).toLowerCase();
          const unsupported = ["chrome:android", "safari:ios"];
          fileTest = unsupported.indexOf(`${browserName}:${platformName}`) < 0;
        })
        .resizeWindow(1024, 600)
        .smartURL(
          langprefix
            ? `/${langprefix}/contact/imagefile_file`
            : "/contact/imagefile_file"
        )
        .savefullScreenShot("01", langprefix)
        .perform(() => {
          if (fileTest) {
            browser
              // File field - it is empty, let's add a file.
              .waitTillElementPresent(
                '[name="files[imagefile_file_0]"]:not([disabled])',
                5000
              )
              .setValueAndChange('[name="files[imagefile_file_0]"]', filePath)
              .pause(100)
              .waitTillElementPresent(
                '[name="imagefile_file[0][description]"]:not([disabled])',
                5000
              )
              .setValueAndChange(
                '[name="imagefile_file[0][description]"]',
                "Test description: lorem ipsum copy"
              )
              // Required file field with a value - let's remove the pre-existing file.
              .click(
                '[name="imagefile_file_req_0_remove_button"]:not([disabled])'
              )
              .pause(100)
              .waitTillElementPresent(
                '[name="files[imagefile_file_req_0]"]:not([disabled])',
                5000
              )
              .pause(100)
              // Unlimited file field.
              .waitTillElementPresent(
                '[name="files[imagefile_file_multi_0][]"]:not([disabled])',
                5000
              )
              .setValueAndChange(
                '[name="files[imagefile_file_multi_0][]"]:not([disabled])',
                filePath
              )
              .pause(100)
              .waitTillElementPresent(
                '[name="files[imagefile_file_multi_1][]"]:not([disabled])',
                5000
              )
              .pause(100)
              // The mandatory 'Limited files with a pre-existing value'
              // field. Removing the pre-existing value.
              .waitTillElementPresent(
                '[name="imagefile_file_limited_0_remove_button"]:not([disabled])',
                5000
              )
              .click('[name="imagefile_file_limited_0_remove_button"]')
              .pause(100)
              .waitTillElementPresent(
                '[name="files[imagefile_file_limited_0][]"]:not([disabled])',
                5000
              );
          } else {
            browser
              // Required file field with a value - let's remove the pre-existing file.
              .waitTillElementPresent(
                '[name="imagefile_file_req_0_remove_button"]:not([disabled])',
                5000
              )
              .click('[name="imagefile_file_req_0_remove_button"]')
              .pause(100)
              .waitTillElementPresent(
                '[name="files[imagefile_file_req_0]"]:not([disabled])',
                5000
              )
              // The mandatory 'Limited files with a pre-existing value'
              // field. Removing the pre-existing value.
              .waitTillElementPresent(
                '[name="imagefile_file_limited_0_remove_button"]:not([disabled])',
                5000
              )
              .click('[name="imagefile_file_limited_0_remove_button"]')
              .pause(100)
              .waitTillElementPresent(
                '[name="files[imagefile_file_limited_0][]"]:not([disabled])',
                5000
              );
          }
        })
        .savefullScreenShot("02", langprefix)
        .click("input#edit-submit")
        // Waiting for error messages (Big Pipe).
        .pause(100)
        .waitTillElementPresent(".messages--error", 5000)
        .perform(() => {
          if (fileTest) {
            browser
              // Remove 'File' value and try to and an invalid one.
              .waitTillElementPresent(
                '[name="imagefile_file_0_remove_button"]:not([disabled])',
                5000
              )
              .click('[name="imagefile_file_0_remove_button"]')
              .pause(100)
              .waitTillElementPresent(
                '[name="files[imagefile_file_0]"]:not([disabled])',
                5000
              )
              .setValueAndChange(
                '[name="files[imagefile_file_0]"]',
                fileInvalidPath
              )
              // Remove the value of the 'Unlimited file' field and try to and
              // an invalid file instead.
              .waitTillElementPresent(
                '[name="imagefile_file_multi_0_remove_button"]:not([disabled])',
                5000
              )
              .click('[name="imagefile_file_multi_0_remove_button"]')
              .pause(100)
              .waitTillElementPresent(
                '[name="files[imagefile_file_multi_0][]"]:not([disabled])',
                5000
              )
              .setValueAndChange(
                '[name="files[imagefile_file_multi_0][]"]',
                fileInvalidPath
              );
          }
        })
        // Create 'Error' screenshot.
        .elements(
          "css selector",
          ".form-item__error-message, .form-item--error-message",
          inlineFormMessagesQueryResults => {
            browser.savefullScreenShot(
              inlineFormMessagesQueryResults.value.length ? "04" : "03",
              langprefix,
              inlineFormMessagesQueryResults.value.length
                ? "Inline error states"
                : "Error states"
            );
          }
        );
    });
  }
};
