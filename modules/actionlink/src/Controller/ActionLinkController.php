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
      NULL,
      'plus',
      'trash',
      'cog',
      'ex',
      'checkmark',
      'show',
      'hide',
    ];

    $variants = [NULL, 'danger'];

    // Loop on icon modifiers.
    foreach ($icon_variants as $icon_name) {
      $group = empty($icon_name) ? 'noicon' : $icon_name;
      $build[$group] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['form-item', 'js-action-links-test']],
        'type_label' => [
          '#type' => 'html_tag',
          '#tag' => 'strong',
          '#value' => $group === 'noicon' ? $this->t('No icon') : $this->t('Icon: %icon-name', [
            '%icon-name' => $icon_name,
          ]),
        ],
      ];

      // Loop on size modifiers.
      foreach (['', 'small', 'extrasmall'] as $size) {
        $delta = empty($size) ? 'default' : $size;
        $build[$group][$delta] = [
          '#type' => 'container',
          '#attributes' => ['class' => ['action-links']],
        ];
        $base_classes = ['action-link'];

        if (!empty($icon_name)) {
          $base_classes[] = Html::getClass('action-link--icon-' . $icon_name);
        }
        if (!empty($size)) {
          $base_classes[] = Html::getClass('action-link--' . $size);
        }

        $base = [
          '#suffix' => ' ',
          '#type' => 'link',
          '#title' => $this->t('Action link'),
          '#url' => Url::fromRoute('<current>'),
          '#attributes' => ['class' => $base_classes],
        ];
        $id_base = 'action-link--' . (empty($icon_name) ? 'no-icon' : $icon_name);

        $build[$group][$delta][] = [
          '#type' => 'container',
          'label' => [
            '#type' => 'html_tag',
            '#tag' => 'small',
            '#value' => empty($size) ? $this->t('Default size') : $this->t('Size: %size', [
              '%size' => $size,
            ]),
          ],
        ];

        // Loop on action modifiers.
        foreach ($variants as $type_variant) {
          // Leading icons.
          $build[$group][$delta][] = NestedArray::mergeDeep($base, [
            '#attributes' => [
              'id' => Html::getUniqueId($id_base),
              'class' => $type_variant ? [Html::getClass('action-link--' . $type_variant)] : [],
            ],
          ]);
        }
      }
    }

    return $build;
  }

}
