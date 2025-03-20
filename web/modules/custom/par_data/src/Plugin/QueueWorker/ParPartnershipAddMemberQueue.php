<?php

namespace Drupal\par_data\Plugin\QueueWorker;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;

/**
 * Processes coordinated business members and adds them to a partnership.
 *
 * @QueueWorker(
 *   id = "par_partnership_add_members",
 *   title = @Translation("PAR Partnership - Add Members"),
 *   cron = {"time" = 60}
 * )
 */
class ParPartnershipAddMemberQueue extends QueueWorkerBase {

  /**
   * Getter for the PAR Data Manager serice.
   */
  public function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function processItem($data) {
    $par_data_partnership = ParDataPartnership::load($data['partnership']);
    $member = $data['row'];
    $existing = $data['existing'];

    // Create all the new entities required to save the organisation to the partnership.
    $entities = $this->createMemberEntities($member);

    if ($existing === FALSE) {
      // Save all the new entities and add as references to the organisation.
      if (isset($entities['par_data_premises']) && $entities['par_data_premises'] instanceof ParDataEntityInterface) {
        $par_data_premises = $entities['par_data_premises'];
        $par_data_premises->save();
      }
      if (isset($entities['par_data_person']) && $entities['par_data_person'] instanceof ParDataEntityInterface) {
        $par_data_person = $entities['par_data_person'];
        $par_data_person->save();
      }
      if (isset($entities['par_data_legal_entity']) && $entities['par_data_legal_entity'] instanceof ParDataEntityInterface) {
        $par_data_legal_entity = $entities['par_data_legal_entity'];
        $par_data_legal_entity->save();
      }

      // Save the Organisation itself with the correct references.
      if (isset($entities['par_data_organisation']) && $entities['par_data_organisation'] instanceof ParDataEntityInterface) {
        $par_data_organisation = $entities['par_data_organisation'];
        if (isset($par_data_premises)) {
          $par_data_organisation->set('field_premises', [$par_data_premises->id()]);
        }
        if (isset($par_data_person)) {
          $par_data_organisation->set('field_person', [$par_data_person->id()]);
        }
        if (isset($par_data_legal_entity)) {
          $par_data_organisation->set('field_legal_entity', [$par_data_legal_entity->id()]);
        }

        $par_data_organisation->save();
      }
    }
    else {
      $par_data_organisation = ParDataOrganisation::load($existing);
    }

    // If no organisation by now this item must be skipped.
    if (!isset($par_data_organisation) || !$par_data_organisation->id()) {
      return;
    }

    // Add the organisation to the coordinated business entity and add to the partnership.
    if (isset($entities['par_data_coordinated_business']) && $entities['par_data_coordinated_business'] instanceof ParDataEntityInterface) {
      // @TODO Check that the organisation isn't already a coordinated business entity on this partnership.

      $par_data_coordinated_business = $entities['par_data_coordinated_business'];
      $par_data_coordinated_business->set('field_organisation', [$par_data_organisation->id()]);
      $par_data_coordinated_business->save();

      $par_data_partnership->get('field_coordinated_business')->appendItem($par_data_coordinated_business->id());
      $par_data_partnership->save();
    }
  }

  /**
   * Helper to translate CSV data into entity config.
   *
   * @param $member
   *   The CSV data to process.
   *
   * @return array
   *   An array of created but unsaved entities keyed by entity type.
   */
  public function createMemberEntities($member) {
    $entities = [];
    foreach ($member as $type => $fields) {
      [$entity_type_name, $entity_bundle_name] = explode(':', $type . ':');
      $entity_type = $this->getParDataManager()->getParEntityType($entity_type_name);
      $entity_storage = $this->getParDataManager()->getEntityTypeStorage($entity_type->id());
      $entity_bundle = $this->getParDataManager()->getParBundleEntity($entity_type->id(), $entity_bundle_name);

      $entity_default_values = [
        'type' => $entity_bundle->id(),
        'uid' => 1,
      ];

      $entity_values = [];
      foreach ($fields as $field => $properties) {
        $field_definition = $this->getParDataManager()->getFieldDefinition($entity_type->id(), $field, $entity_bundle->id());

        if (is_array($properties)) {
          $entity_values[$field] = [];
          foreach ($properties as $property => $value) {
            $entity_values[$field][$property] = $this->processFieldValue($entity_type, $entity_bundle, $field_definition, $value);
          }
        } else {
          $value = $properties;
          $entity_values[$field] = $this->processFieldValue($entity_type, $entity_bundle, $field_definition, $value);
        }
      }

      // Do not create an entity with no useful values.
      if (!empty($entity_values)) {
        $entities[$entity_type->id()] = $entity_storage->create($entity_default_values + $entity_values);
      }
    }
    return $entities;
  }

  public function processFieldValue($entity_type, $entity_bundle, $field_definition, $value) {
    // Fields with limited values may need to be converted to their stored value.
    if ($allowed_values = $entity_bundle->getAllowedValues($field_definition->getName())) {
      $search_key = array_search($value, $allowed_values, TRUE);
      if ($search_key) {
        $value = $search_key;
      }
    }

    // Date fields will need to be transformed.
    if ($field_definition->getType() == 'date' || $field_definition->getType() == 'daterange') {
      $date = new DrupalDateTime($value);
      $value = $date->format('Y-m-d');
    }

    return $value;
  }
}
