<?php

namespace Drupal\par_forms\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\par_data\ParDataManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
* A controller for handling autocomplete callbacks.
*/
class ParAutocompleteController extends ControllerBase {

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $parDataManager;

  /**
   * Constructs a ParDataPermissions instance.
   *
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par data manager.
   */
  public function __construct(ParDataManagerInterface $par_data_manager) {
    $this->parDataManager = $par_data_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('par_data.manager'));
  }

  /**
   * Get the ParDataManager service.
   *
   * @return ParDataManagerInterface
   */
  public function getParDataManager(): ParDataManagerInterface {
    return $this->parDataManager;
  }

  /**
  * The main callback for autocomplete.
  */
  public function callback(Request $request) {
    $input = $request->query->get('q');
    $input = Xss::filter($input);
    if (!$input) {
      return new JsonResponse([]);
    }

    $conditions = [
      [
        'AND' => [
          ['authority_name', "%$input%", 'LIKE']
        ],
      ],
    ];
    $matching_authorities = $this->getParDataManager()->getEntitiesByQuery('par_data_authority', $conditions, 10);
    $matching_options = $this->getParDataManager()->getEntitiesAsAutocomplete($matching_authorities);

    return new JsonResponse($matching_options);
  }

  /**
   * Extracts the entity ID from the autocompletion result.
   *
   * @param string $input
   *   The input coming from the autocompletion result.
   *
   * @return mixed|null
   *   An entity ID or NULL if the input does not contain one.
   */
  public static function extractEntityIdFromAutocompleteInput($input) {
    $match = NULL;

    // Take "label (entity id)', match the ID from inside the parentheses.
    // @todo Add support for entities containing parentheses in their ID.
    // @see https://www.drupal.org/node/2520416
    if (preg_match("/.+\s\(([^\)]+)\)/", $input, $matches)) {
      $match = $matches[1];
    }

    return $match;
  }

}
