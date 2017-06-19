<?php

namespace Drupal\par_data_entities\Entity;

use Drupal\trance\Trance;

/**
 * Defines the par_entity entity.
 *
 * @ingroup par_entity
 *
 * @ContentEntityType(
 *   id = "par_entity",
 *   label = @Translation("PAR Entity"),
 *   bundle_label = @Translation("par_entity type"),
 *   handlers = {
 *     "storage" = "Drupal\trance\TranceStorage",
 *     "storage_schema" = "Drupal\trance\TranceStorageSchema",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\trance\TranceListBuilder",
 *     "views_data" = "Drupal\trance\TranceViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\par_data_entities\Form\ParEntityForm",
 *       "add" = "Drupal\par_data_entities\Form\ParEntityForm",
 *       "edit" = "Drupal\par_data_entities\Form\ParEntityForm",
 *       "delete" = "Drupal\trance\Form\TranceDeleteForm",
 *     },
 *     "access" = "Drupal\trance\Access\TranceAccessControlHandler",
 *   },
 *   base_table = "par_entity",
 *   data_table = "par_entity_field_data",
 *   revision_table = "par_entity_revision",
 *   revision_data_table = "par_entity_field_revision",
 *   admin_permission = "administer par entities",
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
 *     "collection" = "/admin/content/par_entity",
 *     "canonical" = "/admin/content/par_entity/{par_entity}",
 *     "edit-form" = "/admin/content/par_entity/{par_entity}/edit",
 *     "delete-form" = "/admin/content/par_entity/{par_entity}/delete"
 *   },
 *   bundle_entity_type = "par_entity_type",
 *   field_ui_base_route = "entity.par_entity_type.edit_form"
 * )
 */
class ParEntity extends Trance {

}
