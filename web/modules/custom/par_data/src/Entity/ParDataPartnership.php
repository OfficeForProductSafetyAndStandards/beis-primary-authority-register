<?php

namespace Drupal\par_data\Entity;

use Drupal\trance\Trance;

/**
 * Defines the par_data_partnership entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_partnership",
 *   label = @Translation("PAR Partnership"),
 *   label_collection = @Translation("PAR Partnerships"),
 *   label_singular = @Translation("PAR Partnership"),
 *   label_plural = @Translation("PAR Partnerships"),
 *   label_count = @PluralTranslation(
 *     singular = "@count partnership",
 *     plural = "@count partnerships"
 *   ),
 *   bundle_label = @Translation("PAR Partnership type"),
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
 *   base_table = "par_data_partnership",
 *   data_table = "par_data_partnership_field_data",
 *   revision_table = "par_data_partnership_revision",
 *   revision_data_table = "par_data_partnership_field_revision",
 *   admin_permission = "administer par_data_partnership entities",
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
 *     "collection" = "/admin/content/par_data/par_data_partnership",
 *     "canonical" = "/admin/content/par_data/par_data_partnership/{par_data_partnership}",
 *     "edit-form" = "/admin/content/par_data/par_data_partnership/{par_data_partnership}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_partnership/{par_data_partnership}/delete"
 *   },
 *   bundle_entity_type = "par_data_partnership_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_partnership_type.edit_form"
 * )
 */
class ParDataPartnership extends Trance {

}
