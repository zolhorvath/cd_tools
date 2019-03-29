<?php

namespace Drupal\button\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Layout\LayoutPluginManagerInterface;
use Drupal\Core\Render\Element;

/**
 * Ships test page for tables.
 */
class ButtonTestForm extends FormBase {

  /**
   * The layout plugin manager.
   *
   * @var \Drupal\Core\Layout\LayoutPluginManagerInterface
   */
  protected $layoutPluginManager;

  /**
   * Constructs the Button Test form.
   *
   * @param \Drupal\Core\Layout\LayoutPluginManagerInterface $layout_plugin_manager
   *   The layout plugin manager.
   */
  public function __construct(LayoutPluginManagerInterface $layout_plugin_manager) {
    $this->layoutPluginManager = $layout_plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.core.layout')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'button_test_form';
  }

  /**
   * Returns a renderable array for a test page.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $disabled = FALSE) {

    //
    // Buttons.
    //
    $buttons = [
      'actions_default' => [
        'title' => $this->t('Default buttons'),
        'weight' => 0,
      ],
      'actions_small' => [
        'title' => $this->t('Small buttons'),
        'extra_classes' => ['button--small'],
        'weight' => 2,
      ],
    ];
    foreach ($buttons as $key => $value) {
      $form[$key] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['trailer']],
        '#weight' => $value['weight'] ?? 0,
      ];
      $form[$key]['title'] = [
        '#type' => 'item',
        '#name' => $key,
        '#title' => $value['title'] ?? '',
        '#markup' => $this->t('A <em>Submit</em> button (type: primary), a <em>Delete</em> button (type: danger) and a default <em>Cancel</em> button'),
      ];
      $form[$key][$key . '_actions'] = ['#type' => 'actions'];
      $form[$key][$key . '_actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Primary'),
        '#button_type' => 'primary',
        '#attributes' => [
          'class' => $value['extra_classes'] ?? [],
          'tabindex' => 1,
        ],
        '#disabled' => $disabled,
      ];
      $form[$key][$key . '_actions']['danger'] = [
        '#type' => 'button',
        '#value' => $this->t('Danger'),
        '#button_type' => 'danger',
        '#attributes' => [
          'class' => $value['extra_classes'] ?? [],
          'tabindex' => 1,
        ],
        '#disabled' => $disabled,
      ];
      $form[$key][$key . '_actions']['cancel'] = [
        '#type' => 'button',
        '#value' => $this->t('Default'),
        '#attributes' => [
          'class' => $value['extra_classes'] ?? [],
          'tabindex' => 1,
        ],
        '#disabled' => $disabled,
      ];
    }

    //
    // Links as buttons.
    //
    $links = [
      'links_default' => [
        'title' => $this->t('Links as buttons'),
        'weight' => 1,
        'extra_classes' => [
          $disabled ? 'is-disabled' : NULL,
        ],
      ],
      'links_small' => [
        'title' => $this->t('Links as small buttons'),
        'weight' => 3,
        'extra_classes' => [
          'button--small',
          $disabled ? 'is-disabled' : NULL,
        ],
      ],
    ];
    foreach ($links as $key => $value) {
      $form[$key] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['trailer']],
        '#weight' => $value['weight'] ?? 0,
      ];
      $form[$key]['title'] = [
        '#type' => 'item',
        '#name' => $key,
        '#title' => $value['title'] ?? '',
        '#markup' => $this->t('A <em>Submit</em> button (type: primary), a <em>Delete</em> button (type: danger) and a default <em>Cancel</em> button'),
      ];
      $form[$key][$key . '_actions'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['form-actions']],
      ];
      $form[$key][$key . '_actions']['submit'] = [
        '#type' => 'link',
        '#title' => $this->t('Primary'),
        '#url' => Url::fromRoute('<current>', [], [
          'attributes' => [
            'class' => array_merge([
              'button',
              'button--primary',
            ], empty($value['extra_classes']) ? [] : $value['extra_classes']),
          ],
        ]),
      ];
      $form[$key][$key . '_actions']['delete'] = [
        '#type' => 'link',
        '#title' => $this->t('Danger'),
        '#url' => Url::fromRoute('<current>', [], [
          'attributes' => [
            'class' => array_merge([
              'button',
              'button--danger',
            ], empty($value['extra_classes']) ? [] : $value['extra_classes']),
          ],
        ]),
      ];
      $form[$key][$key . '_actions']['cancel'] = [
        '#type' => 'link',
        '#title' => $this->t('Default'),
        '#url' => Url::fromRoute('<current>', [], [
          'attributes' => [
            'class' => array_merge([
              'button',
            ], empty($value['extra_classes']) ? [] : $value['extra_classes']),
          ],
        ]),
      ];
    }

    //
    // Node edit actions.
    //
    $form['node_edit'] = [
      '#type' => 'container',
      '#weight' => 4,
      '#group' => 'content_bottom',
      '#attributes' => ['class' => ['trailer']],
    ];
    $form['node_edit']['title'] = [
      '#type' => 'item',
      '#name' => 'node_edit',
      '#title' => $this->t('Node edit actions'),
      '#markup' => $this->t('A <em>Save</em> button (type: primary), a <em>Preview</em> button (default) and a <em>Delete</em> button (type: danger)'),
    ];
    $form['node_edit']['node_edit_actions'] = [
      '#type' => 'actions',
    ];
    $form['node_edit']['node_edit_actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];
    $form['node_edit']['node_edit_actions']['preview'] = [
      '#type' => 'button',
      '#value' => $this->t('Preview'),
    ];
    $form['node_edit']['node_edit_actions']['delete'] = [
      '#type' => 'button',
      '#value' => $this->t('Delete'),
      '#button_type' => 'danger',
    ];

    //
    // Confirm form actions.
    //
    $form['confirm_form'] = [
      '#type' => 'container',
      '#weight' => 5,
      '#group' => 'content_bottom',
      '#attributes' => ['class' => ['trailer']],
    ];
    $form['confirm_form']['title'] = [
      '#type' => 'item',
      '#name' => 'confirm_form',
      '#title' => $this->t('Confirm form actions'),
      '#markup' => $this->t('A primary <em>Delete</em> button and a cancel link'),
    ];
    $form['confirm_form']['confirm_form_actions'] = [
      '#type' => 'actions',
    ];
    $form['confirm_form']['confirm_form_actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete'),
      '#button_type' => 'primary',
    ];
    $form['confirm_form']['confirm_form_actions']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Cancel'),
      '#url' => Url::fromRoute('<current>', [], [
        'attributes' => [
          'class' => ['button'],
        ],
      ]),
    ];

    // Use side-by-side layout if it is available.
    if ($sbs_layout_definition = $this->layoutPluginManager->getDefinition('side_by_side', FALSE)) {
      $sbs_layout = $this->layoutPluginManager->createInstance('side_by_side');
      $fill = [];
      $fill['#process'][] = '\Drupal\Core\Render\Element\RenderElement::processGroup';
      $fill['#pre_render'][] = '\Drupal\Core\Render\Element\RenderElement::preRenderGroup';
      // Add the regions to the $build in the correct order.
      $regions = array_fill_keys($sbs_layout_definition->getRegionNames(), $fill);

      foreach (Element::children($form) as $elem_key) {
        if (!empty($form[$elem_key]['region']) && empty($form[$elem_key]['#group'])) {
          $form[$elem_key]['#group'] = $form[$elem_key]['region'];
          continue;
        }
        if (!empty($form[$elem_key]['#group'])) {
          continue;
        }
        $form[$elem_key]['#group'] = $sbs_layout_definition->getDefaultRegion();
      }

      $form['_layout'] = $sbs_layout->build($regions);
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
