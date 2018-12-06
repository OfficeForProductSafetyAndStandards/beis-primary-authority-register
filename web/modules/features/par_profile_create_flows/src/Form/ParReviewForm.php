<?php

namespace Drupal\par_profile_create_flows\Form;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_profile_create_flows\ParFlowAccessTrait;
use Drupal\user\Entity\User;

/**
 * The form for the partnership details.
 */
class ParReviewForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'Profile review';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataPerson $par_data_person */
    /** @var User $account */

    $this->getFlowDataHandler()->setFormPermValue("full_name", $par_data_person->getFullName());
    $this->getFlowDataHandler()->setFormPermValue("work_phone", $par_data_person->getWorkPhone());
    $this->getFlowDataHandler()->setFormPermValue("mobile_phone", $par_data_person->getMobilePhone());
    $this->getFlowDataHandler()->setFormPermValue("email", $par_data_person->getEmailWithPreferences());

    $cid_role_select = $this->getFlowNegotiator()->getFormKey('par_choose_role');
    $role = $this->getFlowDataHandler()->getDefaultValues('role', NULL, $cid_role_select);

    $cid_link_account = $this->getFlowNegotiator()->getFormKey('par_profile_link');
    $user_id = $this->getFlowDataHandler()->getDefaultValues('user_id', NULL, $cid_link_account);

    if (!$role) {
      $this->getFlowDataHandler()->setFormPermValue("user_status", 'none');
    }
    elseif(!empty($user_id)) {
      $this->getFlowDataHandler()->setFormPermValue("user_status", 'existing');
    }
    else {
      $this->getFlowDataHandler()->setFormPermValue("user_status", 'invited');
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $form['personal'] = [
      '#type' => 'fieldset',
      'name' => [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#title' => 'Name',
        [
          '#markup' => $this->getFlowDataHandler()->getDefaultValues('full_name', '(none)'),
        ]
      ],
    ];

    $form['contact_details'] = [
      '#type' => 'fieldset',
      'email' => [
        '#type' => 'fieldset',
        '#title' => 'Email',
        '#attributes' => ['class' => 'form-group'],
        [
          '#markup' => $this->getFlowDataHandler()->getDefaultValues('email', '(none)'),
        ]
      ],
      'work_phone' => [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#title' => 'Work phone',
        [
          '#markup' => $this->getFlowDataHandler()->getDefaultValues('work_phone', '(none)'),
        ]
      ],
      'mobile_phone' => [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#title' => 'Mobile phone',
        [
          '#markup' => $this->getFlowDataHandler()->getDefaultValues('mobile_phone', '(none)'),
        ]
      ],
    ];

    switch ($this->getFlowDataHandler()->getDefaultValues("user_status", NULL)) {
      case 'existing':

        $form['intro'] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'form-group'],
          '#title' => 'User account',
          [
            '#markup' => "A user account already exists for this person.",
          ],
        ];

        break;

      case 'invited':

        $form['intro'] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'form-group'],
          '#title' => 'User account',
          [
            '#markup' => "An invitation will be sent to this person to invite them to join the Primary Authority Register.",
          ],
        ];

        break;

      case 'none':
      default:

        $form['intro'] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'form-group'],
          '#title' => 'User account',
          [
            '#markup' => "A user account will not be created for this person.",
          ],
        ];
    }

    return parent::buildForm($form, $form_state);
  }

  public function createEntities() {
    // Get the cache IDs for the various forms that needs needs to be extracted from.
    $contact_details_cid = $this->getFlowNegotiator()->getFormKey('par_add_contact');
    $link_account_cid = $this->getFlowNegotiator()->getFormKey('par_profile_link');

    $par_data_person = ParDataPerson::create([
      'type' => 'person',
      'salutation' => $this->getFlowDataHandler()->getTempDataValue('salutation', $contact_details_cid),
      'first_name' => $this->getFlowDataHandler()->getTempDataValue('first_name', $contact_details_cid),
      'last_name' => $this->getFlowDataHandler()->getTempDataValue('last_name', $contact_details_cid),
      'work_phone' => $this->getFlowDataHandler()->getTempDataValue('work_phone', $contact_details_cid),
      'mobile_phone' => $this->getFlowDataHandler()->getTempDataValue('mobile_phone', $contact_details_cid),
      'email' => $this->getFlowDataHandler()->getTempDataValue('email', $contact_details_cid),
    ]);

    // If there is an existing user attach it to this person.
    $user_id = $this->getFlowDataHandler()->getDefaultValues('user_id', NULL, $link_account_cid);
    $account = $user_id ? User::load($user_id) : NULL;
    if ($account) {
      $par_data_person->setUserAccount($account);
    }

    return [
      'par_data_person' => $par_data_person,
      'account' => $account ?: NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataPerson $par_data_person */
    /** @var User $account */

    $cid_role_select = $this->getFlowNegotiator()->getFormKey('par_choose_role');
    $select_authority_cid = $this->getFlowNegotiator()->getFormKey('par_add_institution');
    $select_organisation_cid = $this->getFlowNegotiator()->getFormKey('par_add_institution');
    $cid_invitation = $this->getFlowNegotiator()->getFormKey('par_profile_invite');

    if ($par_data_person->save()) {
      $role = $this->getFlowDataHandler()->getTempDataValue('role', $cid_role_select);

      // If some authorities have been selected and either
      // an authority role has been selected or no user is being created.
      $authority_ids = $this->getFlowDataHandler()->getTempDataValue('par_data_authority_id', $select_authority_cid);
      if ($authority_ids && (in_array($role, ['par_authority', 'par_enforcement']) || !$role)) {
        $par_data_person->updateAuthorityMemberships($authority_ids, TRUE);
      }

      // If some organisations have been selected and either
      // an organisation role has been selected or no user is being created.
      $organisation_ids = $this->getFlowDataHandler()->getTempDataValue('par_data_organisation_id', $select_organisation_cid);
      if ($organisation_ids && ($role === 'par_organisation' || !$role)) {
        $par_data_person->updateOrganisationMemberships($organisation_ids, TRUE);
      }

      // Send the invite.
      if (isset($invite)) {
        $invite->save();
      }

      // We also need to clear the relationships caches once
      // any new relationships have been saved.
      $par_data_person->getRelationships(NULL, NULL, TRUE);

      // Also invalidate the user account cache if there is one.
      if ($account) {
        \Drupal::entityTypeManager()->getStorage('user')->resetCache([$account->id()]);
      }

      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('User profile could not be created for: %account');
      $replacements = [
        '%account' => $par_data_person->id(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }
  }

}
