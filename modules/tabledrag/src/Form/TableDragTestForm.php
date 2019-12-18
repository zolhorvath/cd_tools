<?php

namespace Drupal\tabledrag\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Forms for draggable table examples.
 */
class TableDragTestForm extends FormBase {

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Constructs a TableDragTestForm object.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   */
  public function __construct(StateInterface $state) {
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('state'));
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'tabledrag_testform';
  }

  /**
   * Builds a draggable test table.
   *
   * @param array $rows
   *   The rows of the table. If the related state key is empty, this is used
   *   for building the table rows.
   * @param string $table_id
   *   The id of the table. Optional. This is also used as state key for
   *   retrieving the saved values.
   * @param string $group_prefix
   *   Used as id prefix for the tabledrag related form elements. Optional.
   * @param bool $indentation
   *   Whether indentation (== row hierarchy) is enabled. Optional.
   *
   * @return array
   *   The renderable draggable table.
   */
  protected function buildTestTable(array $rows = [], $table_id = 'tabledrag_test_table', $group_prefix = 'tabledrag-test', $indentation = TRUE) {
    $tabledrag = [
      [
        'action' => 'order',
        'relationship' => 'sibling',
        'group' => "$group_prefix-weight",
      ],
    ];

    if ($indentation) {
      $tabledrag[] = [
        'action' => 'match',
        'relationship' => 'parent',
        'group' => "$group_prefix-parent",
        'subgroup' => "$group_prefix-parent",
        'source' => "$group_prefix-id",
        'hidden' => TRUE,
      ];
      $tabledrag[] = [
        'action' => 'depth',
        'relationship' => 'group',
        'group' => "$group_prefix-depth",
        'hidden' => TRUE,
      ];
    }

    $table = [
      '#type' => 'table',
      '#header' => [
        [
          'data' => $this->t('Text'),
          'colspan' => 2,
        ],
      ],
      '#tabledrag' => $tabledrag,
      '#attributes' => ['id' => $table_id],
      '#attached' => ['library' => ['tabledrag_test/tabledrag']],
    ];

    $rows = $this->state->get($table_id) ?? $rows;

    $parent_options = [
      '' => 'Root',
    ];

    foreach ($rows as $id => $row) {
      if (!is_array($row)) {
        $row = [];
      }

      $rows[$id] = $row += [
        'id' => $id,
        'title' => "Row with id $id",
        'parent' => '',
        'weight' => 0,
        'depth' => 0,
        'classes' => [],
        'draggable' => TRUE,
        'title_rows' => 1,
      ];

      $rows[$id]['weight'] = (int) $rows[$id]['weight'];
      $rows[$id]['depth'] = (int) $rows[$id]['depth'];
      $rows[$id]['title_rows'] = (int) $rows[$id]['title_rows'];

      $parent_options[(string) $id] = $rows[$id]['title'];
    }

    foreach ($rows as $id => $row) {
      $id = $row['id'];
      if (!empty($row['draggable'])) {
        $row['classes'][] = 'draggable';
      }

      $table[$id] = [
        'first_cell' => [
          'indentation' => [
            '#theme' => 'indentation',
            '#size' => $indentation ? $row['depth'] : 0,
          ],
        ],
        'values' => [
          'id' => [
            '#type' => 'hidden',
            '#value' => $id,
            '#parents' => [$table_id, $id, 'id'],
            '#attributes' => ['class' => ["$group_prefix-id"]],
          ],
          'title' => [
            '#type' => 'hidden',
            '#parents' => [$table_id, $id, 'title'],
            '#value' => $row['title'],
          ],
          'draggable' => [
            '#type' => 'hidden',
            '#parents' => [$table_id, $id, 'draggable'],
            '#value' => !empty($row['draggable']) ? 1 : 0,
          ],
          'title_rows' => [
            '#type' => 'hidden',
            '#parents' => [$table_id, $id, 'title_rows'],
            '#value' => $row['title_rows'],
          ],
        ],
        '#attributes' => ['class' => $row['classes']],
      ];

      if ($row['title_rows'] > 0) {
        $table[$id]['first_cell']['content']['title'] = [
          '#plain_text' => $row['title'],
        ];
      }

      if ($row['title_rows'] > 1) {
        for ($i = 1; $i < $row['title_rows']; $i++) {
          $table[$id]['first_cell']['content']['#theme_wrappers'] = ['container'];
          $table[$id]['first_cell']['content'][] = [
            '#type' => 'html_tag',
            '#tag' => 'div',
            '#plain_text' => $row['title'],
          ];
        }
      }

      if ($indentation) {
        $row_parent_options = $parent_options;
        $table[$id]['parent'] = [
          '#type' => 'select',
          '#default_value' => $row['parent'],
          '#options' => $row_parent_options,
          '#parents' => [$table_id, $id, 'parent'],
          '#attributes' => ['class' => ["$group_prefix-parent"]],
        ];
        $table[$id]['depth'] = [
          '#type' => 'hidden',
          '#default_value' => $row['depth'],
          '#parents' => [$table_id, $id, 'depth'],
          '#attributes' => ['class' => ["$group_prefix-depth"]],
        ];
      }

      $table[$id]['weight'] = [
        '#type' => 'weight',
        '#default_value' => $row['weight'],
        '#parents' => [$table_id, $id, 'weight'],
        '#attributes' => ['class' => ["$group_prefix-weight"]],
      ];
    }

    if ($indentation) {
      $table['#header'][] = [];
      $table['#header'][] = [];
    }
    $table['#header'][] = $this->t('Weight');

    return $table;
  }

  /**
   * Builds the test table form actions.
   *
   * @return array
   *   The renderable array of form actions.
   */
  protected function buildFormActions() {
    return [
      '#type' => 'actions',
      'save' => [
        '#type' => 'submit',
        '#value' => $this->t('Save'),
      ],
      'reset' => [
        '#type' => 'submit',
        '#op' => 'reset',
        '#value' => $this->t('Reset'),
      ],
    ];
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param string|null $prefix
   *   The 'id' and 'state' key prefix for the tables.
   * @param array|null $main_rows
   *   Structure of the main table rows.
   * @param array|null $nested_rows
   *   Structure of the nested tables' rows.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $prefix = '', $main_rows = [], $nested_rows = []) {
    $main_rows = empty($main_rows) ? array_flip(range(1, 5)) : $main_rows;
    $main_table_id = $prefix . '_tabledrag_test_table';
    $form['table_ids'] = [
      '#type' => 'hidden',
      '#value' => $main_table_id,
    ];
    $form[$main_table_id] = $this->buildTestTable($main_rows, $main_table_id);
    $form['actions'] = $this->buildFormActions();

    if (!empty($nested_rows)) {
      foreach ($nested_rows as $parent_row_key => $nested_table_row_defs) {
        if (isset($form[$main_table_id][(string) $parent_row_key])) {
          $nested_table_id = $prefix . "_tabledrag_test_nested_$parent_row_key";
          $form['table_ids']['#value'] .= "::$nested_table_id";
          $form[$main_table_id][(string) $parent_row_key]['first_cell']['content'][$nested_table_id] =
          $this->buildTestTable($nested_table_row_defs, $nested_table_id, "tabledrag-nested-$parent_row_key-");
        }
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $operation = isset($form_state->getTriggeringElement()['#op']) ?
      $form_state->getTriggeringElement()['#op'] :
      'save';
    $table_ids = explode('::', $form_state->getValue('table_ids'));

    switch ($operation) {
      case 'reset':
        foreach ($table_ids as $table_state_id) {
          $this->state->delete($table_state_id);
        }
        break;

      default:
        foreach ($table_ids as $table_state_id) {
          $table_state = [];
          foreach ($form_state->getValue($table_state_id) as $row_key => $row_values) {
            $table_state[$row_key] = $row_values;
          }
          $this->sortRows($table_state);
          $this->state->set($table_state_id, $table_state);
        }
        break;
    }
  }

  /**
   * Sorts the table rows based on their weight, parent and depth before save.
   *
   * @param array $table_state
   *   The array of a table's values.
   *
   * @return array
   *   The sorted table rows and their values.
   */
  protected function sortRows(array &$table_state) {
    uasort($table_state, function ($row_a, $row_b) {
      if ($row_a['parent'] === $row_b['parent']) {
        if ($row_a['depth'] == $row_b['depth']) {
          return ((int) $row_a['weight'] <=> (int) $row_b['weight']);
        }

        if ($row_a['depth'] !== $row_b['depth']) {
          return ((int) $row_a['depth'] <=> (int) $row_b['depth']);
        }
      }

      if ($row_a['parent'] !== $row_b['parent']) {
        return $row_a['id'] === $row_b['parent'] ? -1 : 0;
      }

      return 0;
    });
  }

}
