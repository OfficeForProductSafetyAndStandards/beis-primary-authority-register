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
use Drupal\par_data\Entity\ParDataPersonInterface;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\Plugin\ParForm\ParChooseAccount;
use Drupal\par_person_membership_flows\ParFlowAccessTrait;
use Drupal\par_roles\ParRoleManagerInterface;
use Drupal\user\Entity\User;

/**
 * The form for adding an institution to a user.
 */
class ParAddInstitutionForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'Add the institution';

  /**
   * Get the role manager.
   */
  protected function getRoleManager(): ParRoleManagerInterface {
    return \Drupal::service('par_roles.role_manager');
  }

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    $account = $this->getFlowDataHandler()->getParameter('user');
    $this->getFlowDataHandler()->setFormPermValue('user_mail', $account?->getEmail());

    $cid_person_select = $this->getFlowNegotiator()->getFormKey('person_select');
    $cid_institution_select = $this->getFlowNegotiator()->getFormKey('memberships_select');

    $person_id = $this->getFlowDataHandler()->getDefaultValues('user_person', '', $cid_person_select);
    $person = !empty($person_id) ?
      \Drupal::entityTypeManager()->getStorage('par_data_person')
        ->load($person_id) : NULL;

    if ($person instanceof ParDataPersonInterface) {
      $this->getFlowDataHandler()->setParameter('par_data_person', $person);
    }

    $institution_ids = [
      'par_data_authority' => (array) $this->getFlowDataHandler()->getDefaultValues('par_data_authority_id', [], $cid_institution_select),
      'par_data_organisation' => (array) $this->getFlowDataHandler()->getDefaultValues('par_data_organisation_id', [], $cid_institution_select),
    ];

    // Loop through and add the person to the institution.
    $institutions = [];
    $institution_names = [];
    foreach ($institution_ids as $target_type => $institution_ids) {
      foreach ($institution_ids as $institution_id) {
        $institution = \Drupal::entityTypeManager()
          ->getStorage($target_type)
          ->load($institution_id);

        if ($institution instanceof ParDataMembershipInterface) {
          $institutions[] = $institution;
          $institution_names[] = $institution->label();
        }
      }
    }

    $this->getFlowDataHandler()->setParameter('institutions', $institutions);
    $this->getFlowDataHandler()->setFormPermValue('institution_names', $institution_names);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['institution'] = [
      '#type' => 'container',
      '#attributes' => ['class' => 'govuk-form-group'],
      'name' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t("The user %user will be added to the following institutions:", [
          '%user' => $this->getFlowDataHandler()->getFormPermValue('user_mail') ?? '',
        ]),
      ],
      'institutions' => [
        '#theme' => 'item_list',
        '#items' => $this->getFlowDataHandler()->getFormPermValue('institution_names') ?? [],
        '#attributes' => ['class' => ['govuk-list', 'govuk-list--bullet']],
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $account = $this->getFlowDataHandler()->getParameter('user');

    $person = $this->getFlowDataHandler()->getParameter('par_data_person');
    $institutions = $this->getFlowDataHandler()->getParameter('institutions');

    if ($person instanceof ParDataPersonInterface) {
      // Loop through and add the person to the institution.
      foreach ($institutions as $institution) {
        if (!$institution instanceof ParDataMembershipInterface) {
          continue;
        }

        // Add the memberships.
        $institution = $this->getRoleManager()->addMember($institution, $person);

        // Save the institution.
        $institution->save();
      }

      // Save the user account.
      $account->save();

      // We also need to clear the relationships caches once
      // any new relationships have been saved.
      $person->getRelationships(NULL, NULL, TRUE);

      // Also invalidate the user account cache if there is one.
      \Drupal::entityTypeManager()->getStorage('user')->resetCache([$account->id()]);

      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('The person could not be added to the institutions');
      $this->getLogger($this->getLoggerChannel())
        ->error($message);
    }
  }

}
