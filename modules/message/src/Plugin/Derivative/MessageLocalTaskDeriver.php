<?php

namespace Drupal\message\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Derivative class that provides Message test local tasks.
 */
class MessageLocalTaskDeriver extends DeriverBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $message_lengths = [
      'short' => $this->t('Short messages'),
      'long' => $this->t('Longer messages'),
    ];
    $weight = 1;

    foreach ($message_lengths as $length => $title) {
      $this->derivatives['message.test.' . $length] = [
        'title' => $title,
        'route_parameters' => ['length' => $length],
        'weight' => $weight++,
      ] + $base_plugin_definition;
    }

    return $this->derivatives;
  }

}
