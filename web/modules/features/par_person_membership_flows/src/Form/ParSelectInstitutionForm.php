<?php

namespace Drupal\par_person_membership_flows\Form;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\invite\Entity\Invite;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataMembershipInterface;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\Plugin\ParForm\ParChooseAccount;
use Drupal\par_person_membership_flows\ParFlowAccessTrait;
use Drupal\user\Entity\User;

/**
 * The form for selecting the institution to add.
 */
class ParSelectInstitutionForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'Choose which institution to add';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    parent::loadData();

    // Because this form is only to add memberships, do not pre-populate defaults.
    $this->getFlowDataHandler()->setFormPermValue('par_data_authority_id', []);
    $this->getFlowDataHandler()->setFormPermValue('par_data_organisation_id', []);
  }

}
