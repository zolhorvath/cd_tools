<?php

namespace Drupal\autocomplete\Form;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Locale\CountryManagerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides test form for autocomplete textfield.
 */
class AutocompleteForm extends FormBase {

  /**
   * The country manager.
   *
   * @var \Drupal\Core\Locale\CountryManagerInterface
   */
  protected $countryManager;

  /**
   * Constructs test form.
   *
   * @param \Drupal\Core\Locale\CountryManagerInterface $country_manager
   *   The country manager.
   */
  public function __construct(CountryManagerInterface $country_manager) {
    $this->countryManager = $country_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('country_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'autocomplete_test_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['actions'] = ['#type' => 'actions', '#weight' => 1];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
    ];
    $form['country_autocomplete'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Choose countries'),
      '#default_value' => '',
      '#description' => $this->t('Select your favorite countries. Separate them by commas.'),
      '#autocomplete_route_name' => 'autocomplete.country_autocomplete',
    ];

    $id_prefix = 'claro-autocomplete-country';
    $wrapper_id = Html::getUniqueId($id_prefix . '-add-more-wrapper');
    $max = (int) $form_state->getTemporaryValue('delta') ?: 0;

    $elements = [
      '#theme' => 'field_multiple_value_form',
      '#field_name' => $id_prefix,
      '#cardinality' => -1,
      '#cardinality_multiple' => TRUE,
      '#required' => FALSE,
      '#title' => 'Select some other countries',
      '#description' => $this->t('This is the description of the multiple value form widget'),
      '#max_delta' => $max,
      '#tree' => TRUE,
      '#prefix' => '<div id="' . $wrapper_id . '">',
      '#suffix' => '</div>',
    ];

    for ($delta = 0; $delta <= $max; $delta++) {
      $elements[$delta] = [
        'country' => [
          '#title' => $this->t('Country'),
          '#title_display' => 'invisible',
          '#description' => '',
          '#type' => 'textfield',
          '#autocomplete_route_name' => 'autocomplete.country_autocomplete',
          '#delta' => $delta,
          '#weight' => $delta,
        ],
        '_weight' => [
          '#type' => 'weight',
          '#title' => $this->t('Weight for row @number', ['@number' => $delta + 1]),
          '#title_display' => 'invisible',
          // Note: this 'delta' is the FAPI #type 'weight' element's property.
          '#delta' => $max + 1,
          '#default_value' => $delta,
          '#weight' => 100,
        ],
      ];
    }
    $elements[0]['country']['#placeholder'] = $this->t('Colombia');
    // Concatenate needed to make "Colomia" translated. There isn't any
    // translation for 'Colombia (@coutry_code)'.
    $elements[0]['country']['#placeholder'] .= ' (CO)';

    $elements['add_more'] = [
      '#type' => 'submit',
      '#name' => $id_prefix . '_add_more',
      '#value' => $this->t('Add another item'),
      '#limit_validation_errors' => [],
      '#submit' => [[get_class($this), 'addMoreSubmit']],
      '#ajax' => [
        'callback' => [get_class($this), 'addMoreAjax'],
        'wrapper' => $wrapper_id,
      ],
      '#attributes' => [
        'class' => [
          'claro-autocomplete-add-more-submit',
          'form-item',
        ],
      ],
    ];

    $form['autocomplete_multipe'] = $elements;

    return $form;
  }

  /**
   * Submission handler for the "Add another item" button.
   */
  public static function addMoreSubmit(array $form, FormStateInterface $form_state) {
    $triggering_elem = $form_state->getTriggeringElement();

    $element = NestedArray::getValue($form, array_slice($triggering_elem['#array_parents'], 0, -1));

    $delta = (int) ($element['#max_delta'] ?: 0) + 1;
    $form_state->setTemporaryValue('delta', $delta);

    $form_state->setRebuild();
  }

  /**
   * Ajax callback for the "Add another country" button.
   *
   * @return array
   *   The country input items.
   */
  public static function addMoreAjax(array $form, FormStateInterface $form_state) {
    $triggering_elem = $form_state->getTriggeringElement();
    $element = NestedArray::getValue($form, array_slice($triggering_elem['#array_parents'], 0, -1));
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $this->processValues($form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getTemporaryValue('valid_countries') + [
      'country_autocomplete' => [],
      'autocomplete_multipe' => [],
    ];

    $processed_vals = array_map(
      function (array $country_name_arr) {
        return empty($country_name_arr) ? $this->t('empty') : implode('; ', $country_name_arr);
      }, $values);
    $this->messenger()->addStatus($this->t('Submitted <br>Tag values: @tag-input-values.<br>Multiple input values: @multiple-input-values.', [
      '@tag-input-values' => $processed_vals['country_autocomplete'],
      '@multiple-input-values' => $processed_vals['autocomplete_multipe'],
    ]));
  }

  /**
   * Processes values and sets errors if needed.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   An array of valid values keyes by input name.
   */
  public function processValues(FormStateInterface $form_state) {
    $countries = array_map(
      function (TranslatableMarkup $country_name) {
        return (string) $country_name;
      }, $this->countryManager->getList());
    $valid = [];

    //
    // Check tags-style input values.
    //
    $tags_raw = $form_state->getValue('country_autocomplete');
    $tags_match = [];
    preg_match_all('/[,\s]*\K([\w\s,]+[\w]+)[\s]*\(([A-Z]{0,})\)/ui', $tags_raw, $tags_match);
    $unknown_country_names = [];

    foreach ($tags_match[1] as $index => $country_name) {
      if (
        !in_array($country_name, $countries) &&
        !isset($countries[$tags_match[2][$index]])
      ) {
        $unknown_country_names[] = $country_name;
        continue;
      }
      $valid['country_autocomplete'][] = in_array($country_name, $countries) ? $country_name : $countries[$tags_match[2][$index]];
    }

    if (!empty($unknown_country_names)) {
      $form_state->setErrorByName('country_autocomplete', $this->formatPlural(count($unknown_country_names), 'Unknown country provided: @country-name', 'Unknown countries provided: @country-names', [
        '@country-name' => implode('; ', $unknown_country_names),
        '@country-names' => implode('; ', $unknown_country_names),
      ]));
    }

    //
    // Process stardard multi-value inputs.
    //
    $multi_raw = array_filter(
      $form_state->getValue('autocomplete_multipe'),
      function ($value, $key) {
        return ($key !== 'add_more') && !empty($value['country']);
      },
      ARRAY_FILTER_USE_BOTH
    );
    $multi = array_map(
      function ($value) {
        return $value['country'];
      }, $multi_raw);

    foreach ($multi as $delta => $country_name_raw) {
      $country_match = [];
      $country_name = $country_name_raw;
      $country_code = 'NO_COUNTRY_CODE';
      if (
        preg_match('/[\s]*\K([\w\s,]+[\w]+)[\s]*\(([A-Z]{0,})\)/ui', $country_name_raw, $country_match)
      ) {
        list(, $country_name, $country_code) = $country_match;
      }

      if (
        in_array($country_name, $countries) ||
        isset($countries[$country_code])
      ) {
        $valid['autocomplete_multipe'][] = in_array($country_name, $countries) ? $country_name : $countries[$country_code];
        continue;
      }

      $form_state->setErrorByName('autocomplete_multipe][' . $delta . '][country', $this->t('Unknown country provided: @country-name', [
        '@country-name' => $country_name,
      ]));
    }

    $form_state->setTemporaryValue('valid_countries', $valid);

    return $valid;
  }

}
