<?php

namespace Drupal\table\Form;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Drupal\Core\Utility\TableSort;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Ships test page for tables.
 */
class TableTestForm extends FormBase {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs the TableTestController object.
   *
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   */
  public function __construct(DateFormatterInterface $date_formatter, RouteMatchInterface $route_match) {
    $this->dateFormatter = $date_formatter;
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'table_test_form';
  }

  /**
   * Returns a renderable array for a test page.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $current_url = Url::fromRoute(
      $this->routeMatch->getRouteName(),
      $this->routeMatch->getRawParameters()->all()
    );

    $form['table'] = $this->getTableSceleton() + [
      '#caption' => $this->t('Caption of Example Table'),
      '#attributes' => [
        'class' => ['test-table', 'test-table--filled'],
      ],
    ];

    $rows = [
      [
        'title' => 'Sed at eros in nisi pellentesque',
        'type' => $this->t('Basic page'),
        'author' => 'John',
        'status' => $this->t('Published'),
        'updated' => 1545860911,
      ],
      [
        'title' => 'Curabitur varius ligula magna, ut fringilla ante sagittis sit amet – enean mattis quam sed egestas porttitor — ed mauris nulla, gravida id sapien non, dictum pulvinar felis',
        'type' => $this->t('Basic page'),
        'author' => 'Mark',
        'status' => $this->t('Published'),
        'updated' => 1544430128,
      ],
      [
        'title' => 'Sed mauris nulla, gravida id sapien non!',
        'type' => $this->t('Article'),
        'author' => 'Jason',
        'status' => $this->t('Unpublished'),
        'updated' => 1546100119,
        'selected' => TRUE,
      ],
      [
        'title' => 'Aliquam rhoncus',
        'type' => $this->t('Basic page'),
        'author' => 'Jason',
        'status' => $this->t('Unpublished'),
        'updated' => 1546570910,
      ],
    ];

    $order = TableSort::getOrder($form['table']['#header'], \Drupal::request());
    $sort = TableSort::getSort($form['table']['#header'], \Drupal::request());
    $sort_option = [];

    foreach ($rows as $delta => $row) {
      $sort_option[$delta] = $row[$order['sql']];
    }

    array_multisort($sort_option, ($sort === 'asc' ? SORT_ASC : SORT_DESC), $rows, ($sort === 'asc' ? SORT_ASC : SORT_DESC));

    foreach ($rows as $delta => $values) {
      foreach ($values as $key => $value) {
        if ($key === 'selected') {
          continue;
        }
        $renderable = ['#markup' => $value];

        switch ($key) {
          case 'updated':
            $renderable['#markup'] = $this->dateFormatter->format($value, 'long');
            break;

          case 'title':
            $renderable = Link::fromTextAndUrl($value, $current_url)->toRenderable();
            break;
        }

        $form['table'][$delta][$key] = $renderable;
      }
      $form['table'][$delta]['operations'] = [
        '#type' => 'dropbutton',
        '#links' => [
          'dummy_edit' => [
            'title' => $this->t('Edit'),
            'url' => $current_url,
          ],
          'dummy_delete' => [
            'title' => $this->t('Delete'),
            'url' => $current_url,
          ],
        ],
      ];
    }

    if (!empty($rows)) {
      $form['table']['#footer'] = [
        [
          'data' => [
            '',
            [
              'data' => 'Table footer — last change',
              'colspan' => 4,
            ],
            [
              'data' => $this->dateFormatter->format(max(array_column($rows, 'updated')), 'long'),
              'colspan' => 2,
            ],
          ],
        ],
      ];
    }

    // Empty table.
    $form['table_empty'] = $this->getTableSceleton() + [
      '#caption' => $this->t('Caption of Empty Table'),
      '#attributes' => [
        'class' => ['test-table', 'test-table--empty'],
      ],
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
    ];
    $form['actions']['danger'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete items'),
      '#button_type' => 'danger',
    ];
    $form['actions']['cancel'] = [
      '#type' => 'submit',
      '#value' => $this->t('Cancel'),
    ];

    $form['#cache']['contexts'] = [
      'url.query_args',
      'languages:language_interface',
    ];

    return $form;
  }

  /**
   * Returns sceleton for example tables.
   */
  public function getTableSceleton() {
    return [
      '#type' => 'table',
      '#empty' => $this->t('No content available.'),
      '#tableselect' => TRUE,
      '#header' => [
        [
          'data' => $this->t('Title'),
          'field' => 'title',
        ],
        [
          'data' => $this->t('Content type'),
          'field' => 'type',
          'class' => [RESPONSIVE_PRIORITY_LOW],
        ],
        [
          'data' => $this->t('Author'),
          'class' => [RESPONSIVE_PRIORITY_LOW],
        ],
        [
          'data' => $this->t('Status'),
          'field' => 'status',
          'class' => [RESPONSIVE_PRIORITY_MEDIUM],
        ],
        [
          'data' => $this->t('Updated'),
          'field' => 'updated',
          'sort' => 'desc',
        ],
        [
          'data' => $this->t('Operations'),
          'class' => [RESPONSIVE_PRIORITY_MEDIUM],
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
