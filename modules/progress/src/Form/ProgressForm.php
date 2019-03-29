<?php

namespace Drupal\progress\Form;

use Drupal\Core\Form\BaseFormIdInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\progress\ProgressTypes;

/**
 * Form for progress test pages.
 */
class ProgressForm extends FormBase implements BaseFormIdInterface {

  /**
   * The progress type, e.g. 'throbber', 'ajax-progress-small' or 'all'.
   *
   * @var string
   */
  protected $progressType;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'progress_type_' . $this->progressType . '_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getBaseFormId() {
    return 'progress_test_form';
  }

  /**
   * Constructs the ProgressForm.
   */
  public function __construct() {
    $this->progressType = $this->getRouteMatch()->getRawParameter('progress_type') ?? 'all';
  }

  /**
   * Returns the title of the test form.
   *
   * @param string $progress_type
   *   The id of the progress type or 'all' to render every type except
   *   'fullscreen'.
   */
  public function title($progress_type) {
    $types = ProgressTypes::getTypes();
    $type = $types[$progress_type] ?? ['label' => $this->t('Progress Indicators')];
    return $type['label'] ?? $progress_type;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $progress_type = $this->progressType;
    $types = ProgressTypes::getTypes();
    $types_to_render = !empty($types[$progress_type]) ?
      [$progress_type => $types[$progress_type]] :
      array_filter(
        $types,
        function ($key) {
          return $key !== 'fullscreen';
        },
        ARRAY_FILTER_USE_KEY
      );
    $button_required = !empty(array_filter(
      $types_to_render,
      function ($data) {
        return !empty($data['button_context']);
      }
    ));
    $form['#attached'] = ['library' => ['progress/progress']];
    $form['#theme_wrappers']['container'] = [
      '#attributes' => ['class' => ['progress-test']],
    ];
    if ($button_required) {
      $form['button_visible'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Show button context'),
        '#default_value' => '1',
      ];
    }
    foreach ($types_to_render as $type => $data) {
      $form[$type] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => [
            'progress-test__item',
            'progress-test__item--type-' . $type,
            'clearfix',
          ],
        ],
        'title' => [
          '#type' => 'item',
          '#title' => $progress_type === 'all' ? ($data['label'] ?? $type) : NULL,
          '#plain_text' => $data['description'] ?? '',
          '#name' => $type,
        ],
        'canvas' => [],
      ];

      switch ($type) {
        case 'progress':
          $form[$type]['canvas'] = [
            '#theme_wrappers' => [
              'container' => [
                '#attributes' => ['class' => ['progress-test__canvas']],
              ],
            ],
            '#theme' => 'progress_bar',
            '#percent' => 67,
            '#message' => [
              '#markup' => $this->t('Progress message'),
            ],
            '#label' => $this->t('Progress label'),
          ];
          break;

        case 'throbber':
        case 'throbber-message':
        case 'ajax-progress':
        case 'ajax-progress-small':
          $form[$type]['canvas']['button'] = [
            '#type' => 'button',
            '#value' => $this->t('Button'),
            '#states' => [
              'visible' => [
                ':input[name="button_visible"]' => ['checked' => TRUE],
              ],
            ],
          ];

        default:
          $form[$type]['canvas'] += [
            '#type' => 'container',
            '#attributes' => [
              'class' => [
                $type . '-canvas',
                'progress-test__canvas',
              ],
            ],
          ];
          break;
      }
    }

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Nothing to do'),
        '#button_type' => 'primary',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Nothing to do.
  }

}
