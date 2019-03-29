<?php

namespace Drupal\progress;

/**
 * Provides info about progress types.
 */
class ProgressTypes {

  /**
   * Returns progress types.
   *
   * @return array
   *   Progress types keyed by type id.
   */
  public static function getTypes() {
    return [
      'throbber' => [
        'label' => t('Throbber'),
        'button_context' => TRUE,
      ],
      'throbber-message' => [
        'label' => t('Throbber with message'),
        'short' => t('Throbber & message'),
        'button_context' => TRUE,
      ],
      'ajax-progress' => [
        'label' => t('AJAX progress'),
        'short' => t('AJAX bar'),
        'button_context' => TRUE,
      ],
      'ajax-progress-small' => [
        'label' => t('AJAX small progress'),
        'short' => t('Small AJAX bar'),
        'button_context' => TRUE,
      ],
      'progress' => [
        'label' => t('Progress'),
      ],
      'fullscreen' => [
        'label' => t('Fullscreen progress'),
        'short' => t('Fullscreen'),
        'description' => t('Check the center of the page!'),
      ],
    ];
  }

}
