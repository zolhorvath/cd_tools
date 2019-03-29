<?php

namespace Drupal\autocomplete\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Locale\CountryManagerInterface;

/**
 * Defines a route controller for entity autocomplete form elements.
 */
class AutocompleteController extends ControllerBase {

  /**
   * The country manager.
   *
   * @var \Drupal\Core\Locale\CountryManagerInterface
   */
  protected $countryManager;

  /**
   * Constructs the AutocompleteController object.
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
   * Autocomplete the name of a country.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object that contains the typed text.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The matched country names.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
   *   Thrown if the selection settings key is not found in the key/value store
   *   or if it does not match the stored data.
   */
  public function autocomplete(Request $request) {
    $limit = 10;
    $matches = [];
    // Get the typed string from the URL, if it exists.
    if ($input = $request->query->get('q')) {
      $countries = $this->countryManager->getList();
      foreach ($countries as $coutry_code => $country_name) {
        $country_name = (string) $country_name;

        if (
          (stripos($coutry_code, $input) !== 0) &&
          (mb_stripos($country_name, $input) !== 0)
        ) {
          continue;
        }
        $matches[] = [
          'value' => implode(' ', [$country_name, '(' . $coutry_code . ')']),
          'label' => implode(' ', [$country_name, '(' . $coutry_code . ')']),
        ];
        if (count($matches) === $limit) {
          break 1;
        }
      }
    }

    return new JsonResponse($matches);
  }

}
