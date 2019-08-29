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
    $message_test_types = [
      'short' => $this->t('Short messages'),
      'long' => $this->t('Longer messages'),
      'js' => $this->t('JavaScript messages'),
    ];
    $weight = 1;

    foreach ($message_test_types as $type => $title) {
      $this->derivatives['message.test.' . $type] = [
        'title' => $title,
        'route_parameters' => ['type' => $type],
        'weight' => $weight++,
      ] + $base_plugin_definition;
    }

    return $this->derivatives;
  }

}
