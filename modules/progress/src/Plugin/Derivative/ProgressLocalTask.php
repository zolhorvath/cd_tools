<?php

namespace Drupal\progress\Plugin\Derivative;

use Drupal\progress\ProgressTypes;
use Drupal\Component\Plugin\Derivative\DeriverBase;

/**
 * Provides local task definitions for progress test pages.
 */
class ProgressLocalTask extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $this->derivatives = [];
    $weight = -1;
    $progress_types = ProgressTypes::getTypes();

    foreach ($progress_types as $type => $data) {
      $this->derivatives["progress_$type"] = [
        'title' => $data['short'] ?? $data['label'] ?? $type,
        'base_route' => 'progress.test',
        'route_name' => 'progress.test',
        'route_parameters' => [
          'progress_type' => $type,
        ],
        'weight' => $weight++,
      ];
    }

    foreach ($this->derivatives as &$entry) {
      $entry += $base_plugin_definition;
    }

    return $this->derivatives;
  }

}
