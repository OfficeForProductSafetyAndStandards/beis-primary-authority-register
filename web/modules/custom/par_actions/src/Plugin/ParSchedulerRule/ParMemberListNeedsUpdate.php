<?php

namespace Drupal\par_actions\Plugin\ParSchedulerRule;

use Drupal\par_actions\ParSchedulerRuleBase;
use Drupal\par_data\Entity\ParDataPartnership;

/**
 * Approve an enforcement notice.
 *
 * @ParSchedulerRule(
 *   id = "member_list_needs_update",
 *   title = @Translation("Prompt to update the member list."),
 *   entity = "par_data_partnership",
 *   time = "+3 months",
 *   frequency = "2 months",
 *   queue = FALSE,
 *   status = TRUE,
 *   action = "par_send_member_list_notice"
 * )
 */
class ParMemberListNeedsUpdate extends ParSchedulerRuleBase {

  public function query() {
    $entity_type_definition = \Drupal::entityTypeManager()->getDefinition($this->getEntity());
    $query = parent::query();

    // Only for coordinated partnerships.
    $query->condition('partnership_type', 'coordinated');

    // Only active partnerships.
    $query->condition('partnership_status', 'confirmed_rd');

    // Do not include revoked partnerships.
    $revoked = $query
      ->orConditionGroup()
      ->condition('revoked', 0)
      ->condition('revoked', NULL, 'IS NULL');
    $query->condition($revoked);

    // This condition relies on a time comparison with a timestamp field.
    $timestamp = strtotime($this->getTime());
    $query->condition(
      $entity_type_definition->getRevisionMetadataKey('revision_created'),
      $timestamp,
      '<='
    );

    // Query specific revisions only.
    $query->condition(
      $entity_type_definition->getRevisionMetadataKey('revision_log_message'),
      ParDataPartnership::MEMBER_LIST_REVISION_PREFIX,
      'STARTS_WITH'
    );

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function getItems() {
    $storage = $this->getParDataManager()->getEntityTypeStorage($this->getEntity());
    $coordinated_partnerships = $this->getParDataManager()->getEntitiesByProperty(
      'par_data_partnership', 'partnership_type', 'coordinated', FALSE);

    // This is a reverse query, the entities returned do NOT need updating.
    $results = $this->query()->execute();
    $up_to_date = $results ? $storage->loadMultiple($results) : [];

    return array_diff_key($coordinated_partnerships, $up_to_date);
  }
}
