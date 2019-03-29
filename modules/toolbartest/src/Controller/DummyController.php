<?php

namespace Drupal\toolbartest\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for a dummy page content.
 */
class DummyController extends ControllerBase {

  /**
   * Prints a dummy content.
   *
   * @return array
   *   A render array containing a dummy content.
   */
  public function content() {
    $paragraphs = [
      $this->t('The Media module manages the creation, editing, deletion, settings, and display of media. Items are typically images, documents, slideshows, YouTube videos, tweets, Instagram photos, etc. You can reference media items from any other content on your site. For more information, see the <a href=":media">online documentation for the Media module</a>.', [':media' => 'https://www.drupal.org/docs/8/core/modules/media']),
    ];

    $build = [];
    foreach ($paragraphs as $paragraph) {
      $build[] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $paragraph,
      ];
    }

    return $build;
  }

}
