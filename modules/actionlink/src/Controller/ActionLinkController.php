<?php

namespace Drupal\actionlink\Controller;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Contains code for Action Link test route.
 */
class ActionLinkController extends ControllerBase {

  /**
   * Displays page with action links.
   *
   * @param string $variant
   *   Action link variant.
   *
   * @return array
   *   A render array for the test page with messages.
   */
  public function testPage($variant) {
    $build = [];

    $icon_variants = !empty($variant) ? [$variant] : [
      NULL, 'plus', 'trash', 'cog', 'ex', 'checkmark',
    ];

    $variants = ['danger'];

    foreach ($icon_variants as $delta => $icon_name) {
      $build[$delta] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['action-links', 'js-action-links-test']],
      ];
      $base_classes = ['action-link'];

      if (!empty($icon_name)) {
        $base_classes[] = Html::getClass('action-link--icon-' . $icon_name);
      }

      $base = [
        '#type' => 'link',
        '#title' => $this->t('Action link %variant', [
          '%variant' => $icon_name,
        ]),
        '#url' => Url::fromRoute('<current>'),
        '#attributes' => ['class' => $base_classes],
      ];
      $id_base = 'action-link--' . (empty($icon_name) ? 'no-icon' : $icon_name);

      $build[$delta][] = NestedArray::mergeDeep($base, [
        '#attributes' => [
          'id' => Html::getUniqueId($id_base),
        ],
      ]);

      foreach ($variants as $type_variant) {
        $build[$delta][] = ['#markup' => ' '];
        $build[$delta][] = NestedArray::mergeDeep($base, [
          '#attributes' => [
            'id' => Html::getUniqueId($id_base),
            'class' => [Html::getClass('action-link--' . $type_variant)],
          ],
        ]);
      }
    }

    return $build;
  }

}
