<?php

namespace Drupal\par_forms\Controller;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\par_data\ParDataManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
   * @return \Drupal\par_data\ParDataManagerInterface
   */
  public function getParDataManager(): ParDataManagerInterface {
    return $this->parDataManager;
  }

  /**
   * Get the base query.
   *
   * This is plugin dependent.
   */
  protected function getQuery(string $entity_type_id) {
    return $this->getParDataManager()->getEntityQuery($entity_type_id, 'OR', TRUE);
  }

  /**
   * The main callback for autocomplete.
   */
  public function callback(Request $request) {
    // Get the input parameters.
    $input = $request->query->get('q');
    $input = Xss::filter($input);

    // Get the callback parameters.
    $target_type = $request->query->get('target_type');
    $match_operator = $request->query->get('operator') ?? 'CONTAINS';

    // Validate there is enough information to provide a response.
    if (!$input || !$target_type) {
      return new JsonResponse([], 400);
    }

    // Get the base query.
    try {
      $query = $this->getQuery($target_type);
    }
    catch (PluginNotFoundException $e) {
      return new JsonResponse([], 400);
    }

    /** @var \Drupal\Core\Entity\ContentEntityTypeInterface $entity_type */
    $entity_type = \Drupal::entityTypeManager()->getDefinition($target_type);
    /** @var \Drupal\par_data\Entity\ParDataTypeInterface $entity_bundle */
    $entity_bundle = $this->getParDataManager()->getParBundleEntity($target_type);

    // Add the default search conditions.
    $label_fields = $entity_bundle?->getLabelFields();
    if (!empty($label_fields)) {
      // Create the condition group.
      $label_group = $query->orConditionGroup();

      // Search the label field by default.
      if ($label_key = $entity_type->getKey('label')) {
        $label_group->condition($label_key, $input, $match_operator);
      }

      // Add each condition.
      foreach ($label_fields as $field) {
        $label_group->condition($field, $input, $match_operator);
      }

      // Add the condition group.
      $query->condition($label_group);
    }

    // Get and load the results.
    $results = $query->execute();
    $entities = $this->getParDataManager()->getEntityTypeStorage($target_type)->loadMultiple(array_unique($results));

    // Turn the results into an autocomplete friendly list.
    $matching_options = $this->getParDataManager()->getEntitiesAsAutocomplete($entities);

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

    $values = explode(',', $input);
    foreach ($values as $value) {
      // Take "label (entity id)', match the ID from inside the parentheses.
      // @todo Add support for entities containing parentheses in their ID.
      // @see https://www.drupal.org/node/2520416
      if (preg_match("/.+\s\(([^\)]+)\)/", $input, $matches)) {
        $match = $matches[1];
      }
    }

    return $match;
  }

  /**
   * Extracts the entity IDs from the autocompletion result.
   *
   * @param string $input
   *   The input coming from the autocompletion result.
   *
   * @return mixed|null
   *   An entity ID or NULL if the input does not contain one.
   */
  public static function extractEntityIdsFromAutocompleteInput($input) {
    $matches = [];

    $values = explode(',', $input);
    foreach ($values as $value) {
      if ($match = self::extractEntityIdFromAutocompleteInput($value)) {
        $matches[] = $match;
      }
    }

    return $matches;
  }

}
