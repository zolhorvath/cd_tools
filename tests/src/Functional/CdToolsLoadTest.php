<?php

namespace Drupal\Tests\cd_tools\Functional;

use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests that Clarodist Tools does not break Drupal.
 *
 * @group cd_tools
 */
class CdToolsLoadTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var string[]
   */
  public static $modules = [
    'cd_tools',
  ];

  /**
   * Tests that the project does not breaks Drupal.
   */
  public function testCdToolsInstall() {
    // Test that front page loads.
    $url_front = Url::fromRoute('<front>');
    $this->drupalGet($url_front);
    $this->assertSession()->statusCodeEquals(200);
  }

}
