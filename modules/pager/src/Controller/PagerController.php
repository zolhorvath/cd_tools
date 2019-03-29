<?php

namespace Drupal\pager\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides test page for pager test.
 */
class PagerController extends ControllerBase {

  /**
   * Provides markup for for pager test page.
   *
   * @return array
   *   The test page as renderable array.
   */
  public function content() {
    pager_default_initialize(300, 1);
    $build = [];

    $build['pager_views_mini'] = [
      'title' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['text-align-center']],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'strong',
          '#value' => $this->t('Mini pager'),
        ],
      ],
      'pager' => [
        '#theme' => 'views_mini_pager',
      ],
    ];

    $build['pager_claro'] = [
      'title' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['leader', 'text-align-center']],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'strong',
          '#value' => $this->t("Claro's pager on Figma"),
        ],
      ],
      'pager' => [
        '#type' => 'pager',
        '#quantity' => 5,
      ],
    ];

    $build['pager_custom'] = [
      'title' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['leader', 'text-align-center']],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'strong',
          '#value' => $this->t('Customized pager'),
        ],
      ],
      'pager' => [
        '#type' => 'pager',
        '#quantity' => 3,
        '#tags' => [
          $this->t('Custom first'),
          $this->t('Custom prev'),
          NULL,
          $this->t('Custom next'),
          $this->t('Custom last'),
        ],
      ],
    ];

    $build['pager_default'] = [
      'title' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['leader', 'text-align-center']],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'strong',
          '#value' => $this->t('Default pager'),
        ],
      ],
      'pager' => [
        '#type' => 'pager',
      ],
    ];

    return $build;
  }

}
