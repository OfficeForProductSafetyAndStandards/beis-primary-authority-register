<?php

namespace Drupal\par_data\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the par_data_enforcement_notice entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_enforcement_notice",
 *   label = @Translation("PAR Enforcement Notice"),
 *   label_collection = @Translation("PAR Enforcement Notices"),
 *   label_singular = @Translation("PAR Enforcement Notice"),
 *   label_plural = @Translation("PAR Enforcement Notices"),
 *   label_count = @PluralTranslation(
 *     singular = "@count enforcement notice",
 *     plural = "@count enforcement notices"
 *   ),
 *   bundle_label = @Translation("PAR Enforcement Notice type"),
 *   handlers = {
 *     "storage" = "Drupal\par_data\ParDataStorage",
 *     "storage_schema" = "Drupal\trance\TranceStorageSchema",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\trance\TranceListBuilder",
 *     "views_data" = "Drupal\par_data\Views\ParDataViewsData",
 *     "form" = {
 *       "default" = "Drupal\par_data\Form\ParDataForm",
 *       "add" = "Drupal\par_data\Form\ParDataForm",
 *       "edit" = "Drupal\par_data\Form\ParDataForm",
 *       "delete" = "Drupal\par_data\Form\ParDataDeleteForm",
 *     },
 *     "access" = "Drupal\par_data\Access\ParDataAccessControlHandler",
 *   },
 *   base_table = "par_enforcement_notices",
 *   data_table = "par_enforcement_notices_field_data",
 *   revision_table = "par_enforcement_notices_revision",
 *   revision_data_table = "par_enforcement_notices_field_revision",
 *   admin_permission = "administer par_data_enforcement_notice entities",
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
 *     "collection" = "/admin/content/par_data/par_data_enforcement_notice",
 *     "canonical" = "/admin/content/par_data/par_data_enforcement_notice/{par_data_enforcement_notice}",
 *     "edit-form" = "/admin/content/par_data/par_data_enforcement_notice/{par_data_enforcement_notice}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_enforcement_notice/{par_data_enforcement_notice}/delete"
 *   },
 *   bundle_entity_type = "par_data_enforcement_notice_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_enforcement_notice_type.edit_form"
 * )
 */
class ParDataEnforcementNotice extends ParDataEntity {

  /**
   * {@inheritdoc}
   */
  public function inProgress() {
    // Any Enforcement Notices with actions that are awaiting approval are marked as 'in progress'.
    foreach ($this->getEnforcementActions() as $action) {
      if ($action->inProgress()) {
        return TRUE;
      }
    }

    return parent::inProgress();
  }

  /**
   * Get the primary authority for this Enforcement Notice.
   *
   * @param boolean $single
   *
   * @return ParDataEntityInterface|bool
   *   Return false if not referred.
   *
   */
  public function getPrimaryAuthority($single = FALSE) {
    // All referred notices should have an authority referenced in
    // field_primary_authority which is the authority that is now responsible.
    // If it doesn't have this we should get the original authority
    // from the partnership.
    if ($this->get('field_primary_authority')->isEmpty()) {
      $partnership = $this->getPartnership(TRUE);
      return $partnership ? $partnership->getAuthority($single) : NULL;
    }

    $authorities = $this->get('field_primary_authority')->referencedEntities();
    $authority = !empty($authorities) ? current($authorities) : NULL;

    return $single ? $authority : $authorities;
  }

  /**
   * If this is a referred notice get the original notice.
   *
   * @return ParDataEntityInterface|bool
   *   Return false if not referred.
   */
  public function getReferringNotice() {
    foreach ($this->getEnforcementActions() as $action) {
      if ($action->isReferred() && ($referred_from = $action->getActionReferral())) {
          return $referred_from->getEnforcementNotice();
      }
    }

    return FALSE;
  }

  /**
   * Get the Partnership for this Enforcement Notice.
   *
   * @param boolean $single
   *
   */
  public function getPartnership($single = FALSE) {
    $partnerships = $this->get('field_partnership')->referencedEntities();
    $partnership = !empty($partnerships) ? current($partnerships) : NULL;

    return $single ? $partnership : $partnerships;
  }

  /**
   * Get the enforcing authority for this Enforcement Notice.
   */
  public function getEnforcingAuthority() {
    return $this->get('field_enforcing_authority')->referencedEntities();
  }

  /**
   * Get the legal entity for this Enforcement Notice.
   */
  public function getLegalEntity() {
    return $this->get('field_legal_entity')->referencedEntities();
  }

  /**
   * Get the enforcement actions for this Enforcement Notice.
   */
  public function getEnforcementActions() {
    return $this->get('field_enforcement_action')->referencedEntities();
  }

  /**
   * Get the enforced organisation for this Enforcement Notice.
   */
  public function getEnforcedOrganisation() {
    return $this->get('field_organisation')->referencedEntities();
  }

  /**
   * Get the enforcing officer person for the current Enforcement notice.
   */
  public function getEnforcingPerson() {
    return $this->get('field_person')->referencedEntities();
  }

  /**
   * Approve all actions of an enforcement notice.
   */
  public function approve() {
    foreach ($this->getEnforcementActions() as $action) {
      $action->approve();
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Notice Type.
    $fields['notice_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Notice Type'))
      ->setDescription(t('The type of enforcement notice.'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Notice Date.
    $fields['notice_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Notice Date'))
      ->setDescription(t('The date this enforcement notice was issued.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Notice Type.
    $fields['legal_entity_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Legal Entity Name'))
      ->setDescription(t('An optional free text field for entering a legal entity name.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 500,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Summary.
    $fields['summary'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Summary'))
      ->setDescription(t('Summary about this enforcement notice.'))
      ->setRevisionable(TRUE)
      ->addConstraint('par_required')
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 3,
        'settings' => [
          'rows' => 25,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'text_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
