/**
 * @file
 * Captures image widget with different states.
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
  "Image Widgets": function test(browser) {
    const imagePath = path.join(__dirname, "..", "..", "assets", "test.png");
    const imageInvalidPath = path.join(
      __dirname,
      "..",
      "..",
      "assets",
      "test-big.png"
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
            ? `/${langprefix}/contact/imagefile_image`
            : "/contact/imagefile_image"
        )
        .waitTillElementPresent('.js-sbs-menu-item[data-option="all"]', 5000)
        // Click Show all columns needed.
        .element(
          "css selector",
          '.js-sbs-menu-item[data-option="all"]:not(.active)',
          allOptionQuery => {
            browser.perform(done => {
              /* eslint-disable max-nested-callbacks */
              if (allOptionQuery.status > -1) {
                browser.click(
                  '.js-sbs-menu-item[data-option="all"]:not(.active)'
                );
              }
              done();
              /* eslint-enable max-nested-callbacks */
            });
          }
        )
        .savefullScreenShot("01", langprefix)
        // Show only the first column.
        .click('.js-sbs-menu-item[data-option="odd"]:not(.active)')
        .perform(() => {
          if (fileTest) {
            browser
              // Image field - it is empty, let's add an image.
              .setValueAndChange(
                '[name="files[imagefile_image_0]"]:not([disabled])',
                imagePath
              )
              .pause(100)
              .waitTillElementPresent('[name="imagefile_image[0][alt]"]', 5000)
              .setValueAndChange(
                '[name="imagefile_image[0][alt]"]',
                "Test alt: Green square"
              )
              .setValueAndChange(
                '[name="imagefile_image[0][title]"]',
                "Test title: Green square"
              )
              // Required image field with a value - let's remove the pre-existing image.
              .waitTillElementPresent(
                '[name="imagefile_image_req_0_remove_button"]:not([disabled])',
                5000
              )
              .pause(500) // Animation happens in Seven
              .click('[name="imagefile_image_req_0_remove_button"]')
              .waitTillElementPresent(
                '[name="files[imagefile_image_req_0]"]:not([disabled])',
                5000
              )
              // Unlimited image field.
              .waitTillElementPresent(
                '[name="files[imagefile_image_multi_0][]"]:not([disabled])',
                5000
              )
              .pause(100)
              .setValueAndChange(
                '[name="files[imagefile_image_multi_0][]"]',
                imagePath
              )
              .pause(100)
              .waitTillElementPresent(
                '[name="files[imagefile_image_multi_1][]"]:not([disabled])',
                5000
              )
              .pause(100)
              .waitTillElementPresent(
                '[name="files[imagefile_image_multi_def_0][]"]:not([disabled])',
                5000
              )
              // 'Unlimited image with a default picture' field.
              .setValueAndChange(
                '[name="files[imagefile_image_multi_def_0][]"]:not([disabled])',
                imagePath
              )
              .pause(100)
              .waitTillElementPresent(
                '[name="imagefile_image_multi_def[0][alt]"]',
                5000
              )
              .setValueAndChange(
                '[name="imagefile_image_multi_def[0][alt]"]',
                "Test alt: Green square"
              )
              .setValueAndChange(
                '[name="imagefile_image_multi_def[0][title]"]',
                "Test title: Green square"
              )
              // The mandatory 'Limited image with a pre-existing value'
              // field. Removing the pre-existing value.
              .pause(500) // Animation happens in Seven
              .waitTillElementPresent(
                '[name="imagefile_image_limited_1_remove_button"]:not([disabled])',
                5000
              )
              .pause(100)
              .click('[name="imagefile_image_limited_1_remove_button"]')
              .pause(500) // Animation happens in Seven
              .waitTillElementPresent(
                '[name="imagefile_image_limited_0_remove_button"]:not([disabled])',
                5000
              )
              .pause(100)
              .click('[name="imagefile_image_limited_0_remove_button"]')
              .pause(500) // Animation happens in Seven
              .waitTillElementPresent(
                '[name="files[imagefile_image_limited_0][]"]:not([disabled])',
                5000
              );
          } else {
            browser
              // Required image field with a value - remove pre-existing.
              .waitTillElementPresent(
                '[name="imagefile_image_req_0_remove_button"]:not([disabled])',
                5000
              )
              .pause(500) // Animation happens in Seven
              .click('[name="imagefile_image_req_0_remove_button"]')
              .waitTillElementPresent(
                '[name="files[imagefile_image_req_0]"]:not([disabled])',
                5000
              )
              // 'Limited image with a pre-existing value (required)' field.
              // Remove the pre-existing value.
              .pause(500) // Animation happens in Seven
              .waitTillElementPresent(
                '[name="imagefile_image_limited_1_remove_button"]:not([disabled])',
                5000
              )
              .pause(100)
              .click('[name="imagefile_image_limited_1_remove_button"]')
              .pause(500) // Animation happens in Seven
              .waitTillElementPresent(
                '[name="imagefile_image_limited_0_remove_button"]:not([disabled])',
                5000
              )
              .pause(100)
              .click('[name="imagefile_image_limited_0_remove_button"]')
              .pause(500) // Animation happens in Seven
              .waitTillElementPresent(
                '[name="files[imagefile_image_limited_0][]"]:not([disabled])',
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
              // Remove 'Image' value and try to and an invalid image.
              .click('[name="imagefile_image_0_remove_button"]')
              .pause(100)
              .waitTillElementPresent(
                '[name="files[imagefile_image_0]"]:not([disabled])',
                5000
              )
              .setValueAndChange(
                '[name="files[imagefile_image_0]"]:not([disabled])',
                imageInvalidPath
              )
              // Remove the value of the 'Unlimited image' field and try to and
              // an invalid image.
              .waitTillElementPresent(
                '[name="imagefile_image_multi_0_remove_button"]:not([disabled])',
                5000
              )
              .pause(500) // Animation happens in Seven
              .click('[name="imagefile_image_multi_0_remove_button"]')
              .pause(100)
              .waitTillElementPresent(
                '[name="files[imagefile_image_multi_0][]"]:not([disabled])',
                5000
              )
              .setValueAndChange(
                '[name="files[imagefile_image_multi_0][]"]:not([disabled])',
                imageInvalidPath
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
