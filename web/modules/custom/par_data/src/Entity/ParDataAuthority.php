<?php

namespace Drupal\par_data\Entity;

use Drupal\trance\Trance;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the par_data_authority entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_authority",
 *   label = @Translation("PAR Authority"),
 *   label_collection = @Translation("PAR Authorities"),
 *   label_singular = @Translation("PAR Authority"),
 *   label_plural = @Translation("PAR Authorities"),
 *   label_count = @PluralTranslation(
 *     singular = "@count authority",
 *     plural = "@count authorities"
 *   ),
 *   bundle_label = @Translation("PAR Authority type"),
 *   handlers = {
 *     "storage" = "Drupal\trance\TranceStorage",
 *     "storage_schema" = "Drupal\trance\TranceStorageSchema",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\trance\TranceListBuilder",
 *     "views_data" = "Drupal\trance\TranceViewsData",
 *     "form" = {
 *       "default" = "Drupal\trance\Form\ParEntityForm",
 *       "add" = "Drupal\trance\Form\ParEntityForm",
 *       "edit" = "Drupal\trance\Form\ParEntityForm",
 *       "delete" = "Drupal\trance\Form\TranceDeleteForm",
 *     },
 *     "access" = "Drupal\trance\Access\TranceAccessControlHandler",
 *   },
 *   base_table = "par_authorities",
 *   data_table = "par_authorities_field_data",
 *   revision_table = "par_authorities_revision",
 *   revision_data_table = "par_authorities_field_revision",
 *   admin_permission = "administer par_data_authority entities",
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status"
 *   },
 *   links = {
 *     "collection" = "/admin/content/par_data/par_data_authority",
 *     "canonical" = "/admin/content/par_data/par_data_authority/{par_data_authority}",
 *     "edit-form" = "/admin/content/par_data/par_data_authority/{par_data_authority}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_authority/{par_data_authority}/delete"
 *   },
 *   bundle_entity_type = "par_data_authority_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_authority_type.edit_form"
 * )
 */
class ParDataAuthority extends Trance {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    return $fields;
  }

}
